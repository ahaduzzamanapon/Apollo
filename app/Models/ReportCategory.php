<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_name',
        'test_name',
        'price',
        'room_no',
    ];

    public function honorariums()
    {
        return $this->hasMany(DoctorHonorarium::class);
    }
}
