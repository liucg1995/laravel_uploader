<?php

namespace Liucg1995\Uploader\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @param $alpha_id
     * @return mixed
     * 根据 alpha_id  查询文件信息
     */
    public static function find_by_alpha_id($alpha_id)
    {
        return Upload::where(['alpha_id' => $alpha_id])->first();
    }
}
