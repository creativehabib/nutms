<?php

use App\Models\College;
use App\Services\AiConversationPruner;
use App\Services\CollegeMediaImporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schedule;
use Symfony\Component\Console\Command\Command;

Schedule::call(fn (): int => app(AiConversationPruner::class)->prune())
    ->dailyAt('02:30')
    ->name('prune-ai-conversations')
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('colleges:download-media', function (CollegeMediaImporter $importer): int {
    $downloaded = 0;
    $unavailable = 0;
    $failed = 0;

    College::query()
        ->where(fn (Builder $query) => $query->whereNotNull('logo')->orWhereNotNull('banner'))
        ->lazyById()
        ->each(function (College $college) use ($importer, &$downloaded, &$unavailable, &$failed): void {
            foreach (['logo' => 'college-logos', 'banner' => 'college-banners'] as $attribute => $directory) {
                $reference = $college->{$attribute};

                if (blank($reference)) {
                    continue;
                }

                if (Storage::disk('public')->exists($reference)) {
                    if (str_starts_with($reference, $directory.'/')) {
                        continue;
                    }

                    $path = $directory.'/'.basename($reference);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($reference);
                    } else {
                        Storage::disk('public')->move($reference, $path);
                    }
                    $college->forceFill([$attribute => $path])->save();
                    $downloaded++;

                    continue;
                }

                try {
                    $path = $importer->import($reference, $directory);
                    $college->forceFill([$attribute => $path])->save();
                    $downloaded++;
                } catch (\UnexpectedValueException $exception) {
                    $college->forceFill([$attribute => null])->save();
                    $this->warn("College {$college->college_code} {$attribute}: {$exception->getMessage()} Reference cleared.");
                    $unavailable++;
                } catch (\RuntimeException $exception) {
                    $this->warn("College {$college->college_code} {$attribute}: {$exception->getMessage()}");
                    $failed++;
                }
            }
        });

    $this->info("Downloaded {$downloaded} college images; {$unavailable} unavailable references cleared; {$failed} failed.");

    return $failed === 0 ? Command::SUCCESS : Command::FAILURE;
})->purpose('Download National University college logos and banners to public storage');
