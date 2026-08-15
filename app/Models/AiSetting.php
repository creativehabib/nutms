<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = ['is_enabled', 'provider', 'model', 'endpoint', 'api_key', 'system_prompt', 'history_limit'];

    protected $hidden = ['api_key'];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'api_key' => 'encrypted',
            'history_limit' => 'integer',
        ];
    }
}
