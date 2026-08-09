<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionInfo extends Model
{
    protected $guarded = [];

    public function collegeInfo(): BelongsTo
    {
        // AdmissionInfo এর 'college_code' এবং College এর 'code' ম্যাচ করানো হলো
        return $this->belongsTo(College::class, 'college_code', 'code');
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class, 'college_code', 'code');
    }
}
