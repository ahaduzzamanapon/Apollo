<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CenterDetails extends Model
{
    use SoftDeletes;

    protected $table = 'center_details';

    protected $fillable = ['name_bn', 'name_en', 'about', 'address', 'phone', 'logo_image'];


}
