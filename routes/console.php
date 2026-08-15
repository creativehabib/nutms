<?php

use App\Services\AiConversationPruner;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::call(fn (): int => app(AiConversationPruner::class)->prune())
    ->dailyAt('02:30')
    ->name('prune-ai-conversations')
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
