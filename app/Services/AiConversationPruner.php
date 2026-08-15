<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\AiSetting;

class AiConversationPruner
{
    public function prune(): int
    {
        $setting = AiSetting::query()->latest('id')->first();
        $retentionDays = $setting?->retention_days ?? 30;

        return AiConversation::query()
            ->where('updated_at', '<', now()->subDays($retentionDays))
            ->delete();
    }

    public function trim(AiConversation $conversation, int $historyLimit): void
    {
        $retainedIds = $conversation->messages()->latest('id')->limit(max($historyLimit * 2, 20))->pluck('id');

        $conversation->messages()->whereNotIn('id', $retainedIds)->delete();
    }
}
