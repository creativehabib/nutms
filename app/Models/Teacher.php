<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    // কোনো কলামই প্রোটেক্টেড নয়, সব কলামে ডেটা ইনসার্ট করা যাবে
    protected $guarded = [];
}
