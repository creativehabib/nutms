<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionInfo extends Model
{
    protected $guarded = [];

    public function collegeInfo(): BelongsTo
    {
        return $this->belongsTo(College::class, 'college_code', 'college_code');
    }

    /**
     * Get the college associated with the admission info.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class, 'college_code', 'college_code');
    }
}
