<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionInfo extends Model
{
    protected $guarded = [];
    public function collegeInfo()
    {
        // AdmissionInfo এর 'college_code' এবং College এর 'code' ম্যাচ করানো হলো
        return $this->belongsTo(College::class, 'college_code', 'code');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_code', 'code');
    }
}
