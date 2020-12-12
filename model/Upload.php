<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function setAlphaIdAttribute($value)
    {
        $this->attributes['alpha_id'] = strtolower($value) . '1111';
    }



}
