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
    $failed = 0;

    College::query()
        ->where(fn (Builder $query) => $query->whereNotNull('logo')->orWhereNotNull('banner'))
        ->lazyById()
        ->each(function (College $college) use ($importer, &$downloaded, &$failed): void {
            foreach (['logo', 'banner'] as $attribute) {
                $reference = $college->{$attribute};

                if (blank($reference) || Storage::disk('public')->exists($reference)) {
                    continue;
                }

                try {
                    $importer->import($reference);
                    $downloaded++;
                } catch (\RuntimeException $exception) {
                    $this->warn("College {$college->college_code} {$attribute}: {$exception->getMessage()}");
                    $failed++;
                }
            }
        });

    $this->info("Downloaded {$downloaded} college images; {$failed} failed.");

    return $failed === 0 ? Command::SUCCESS : Command::FAILURE;
})->purpose('Download National University college logos and banners to public storage');
