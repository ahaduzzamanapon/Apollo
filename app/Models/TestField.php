<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestField extends Model
{
    use SoftDeletes;

    protected $table = 'test_fields';

    protected $fillable = ['test_id', 'perameter', 'unit', 'ref_val'];


}
