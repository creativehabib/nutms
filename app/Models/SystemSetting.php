<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    public const RETIREMENT_AGE = 'retirement_age';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function retirementAge(): int
    {
        return (int) static::query()->whereKey(self::RETIREMENT_AGE)->value('value') ?: 59;
    }
}
