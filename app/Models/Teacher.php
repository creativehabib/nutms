<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    // কোনো কলামই প্রোটেক্টেড নয়, সব কলামে ডেটা ইনসার্ট করা যাবে
    protected $guarded = [];
}
