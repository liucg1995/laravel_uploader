<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadMulti extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * 获取博客文章的评论
     */
    public function upload()
    {
        return $this->belongsTo('App\Models\Upload', 'upload_id');
    }


    public static function get_multi_info($rid, $model)
    {
        return UploadMulti::where(['rid' => $rid, 'model' => $model])->get();
    }

    public static function save_multi_info($data, $rid, $model)
    {


        $has_data = UploadMulti::where(['rid' => $rid, 'model' => $model])->pluck('id', 'alpha_id');

        if ($data) {
            $i = 1;
            foreach ($data as $alpha_id => $title) {
                $upload_id = Upload::where(['alpha_id' => $alpha_id])->value('id');

                //已经有的修改
                if ($has_data && (($id = array_search($alpha_id, $has_data->toArray())) !== false)) {
                    $upload = UploadMulti::find($id);
                    $upload->rid = $rid;
                    $upload->model = $model;
                    $upload->alpha_id = $alpha_id;
                    $upload->upload_id = $upload_id;
                    $upload->title = $title;
                    $upload->orders = $i;
                    $upload->save();
                    unset($has_data[$id]);
                } else {

                    $upload = new UploadMulti();
                    $upload->rid = $rid;
                    $upload->model = $model;
                    $upload->alpha_id = $alpha_id;
                    $upload->upload_id = $upload_id;
                    $upload->title = $title;
                    $upload->orders = $i;
                    $upload->save();
                }
                $i++;
            }
        }

        //如果有多余的，删除
        if (!$has_data->isEmpty()) {
//            dd($has_data);
            UploadMulti::whereIn('id', array_values($has_data->toArray()))->delete();
        }

    }
}
