<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestCategory extends Model
{
    use SoftDeletes;

    protected $table = 'test_categorys';

    protected $fillable = [
        'category_name',
        'status'
    ];

    public function tests()
    {
        return $this->hasMany(ReportCategory::class, 'category_name', 'id');
    }
}
