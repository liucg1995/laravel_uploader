<?php

use  Liucg1995\Uploader\Models\Upload;
use  Liucg1995\Uploader\Models\UploadMulti;


function form_upload_attach($input_name, $use_model, $item, $params = array())
{

    $params['config_flag'] = isset($params['config_flag']) ? $params['config_flag'] : 'attach';
    return show_webupload_info($input_name, $use_model, $item, $params, 1);
}

function form_upload_attaches($input_name, $use_model, $rid, $params = array())
{

    $params['config_flag'] = isset($params['config_flag']) ? $params['config_flag'] : 'attach';
    return show_webupload_info($input_name, $use_model, $rid, $params);
}

function form_upload_image($input_name, $use_model, $item, $params = array())
{

    $params['config_flag'] = isset($params['config_flag']) ? $params['config_flag'] : 'img';
    return show_webupload_info($input_name, $use_model, $item, $params, 1);
}

function form_upload_images($input_name, $use_model, $rid, $params = array())
{

    $params['config_flag'] = isset($params['config_flag']) ? $params['config_flag'] : 'img';
    return show_webupload_info($input_name, $use_model, $rid, $params);
}


/**
 * 跟据id取得图片信息
 *
 * @param string $alpha_id
 * @return array
 */
function get_info($alpha_id)
{
    if (!$alpha_id) return false;

    $info = Upload::find_by_alpha_id($alpha_id);


    if (!$info) return false;

    return $info;

    $new_info = array();
    /* alpha_id	model 属于哪个模型	file_name 已上传的文件名（包括扩展名）	file_type 文件的Mime类型	file_path 不包括文件名的文件绝对路径
    full_path 包括文件名在内的文件绝对路径	raw_name 不包括扩展名在内的文件名部分	orig_name 上传的文件最初的文件名	client_name 上传的文件在客户端的文件名
    file_ext 文件扩展名包括.	file_size 图像大小，单位是kb	is_image 是否是图像1是,0不是	image_width	image_height	image_type 文件类型，即文件扩展名不包括.
    image_size_str 一个包含width和height的字符串	pid 图片父id	thumb 缩略图标识	crop_mode 裁剪模式,manual手动,auto自动	is_private 是否为内部文件 */

    $new_info['alpha_id'] = $info['alpha_id'];
    $new_info['file_name'] = $info['file_name'];
    $new_info['full_path'] = $info['full_path'];
    $new_info['client_name'] = $info['client_name'];
    $new_info['file_ext'] = ltrim(strtolower($info['file_ext']), '.');
    $new_info['file_size'] = num_kbunit($info['file_size']);

    $new_info['is_image'] = $info['is_image'];
    $new_info['image_width'] = $info['image_width'];
    $new_info['image_height'] = $info['image_height'];
    $new_info['image_size_str'] = $info['image_size_str'];
    return $new_info;
}

/**
 * 跟据关联id取得文件列表
 *
 * @param int $rid
 * @return array
 */
function get_list($rid, $use_model = false)
{

    if ($use_model)
        $multi_info = UploadMulti::get_multi_info($rid, $use_model);
    else {
        return false;
    }
    if (!$multi_info) {
        return false;
    }
    //p($has_data);

    return $multi_info;

    $new_data = array();
    foreach ($has_data as $item) {
        $new_info = array();

        $new_info['alpha_id'] = $item['alpha_id'];
        $new_info['file_name'] = $item['title'];
        $new_info['full_path'] = $item['full_path'];
        $new_info['client_name'] = $item['title'];
        $new_info['file_ext'] = ltrim(strtolower($item['file_ext']), '.');
        $new_info['file_size'] = num_kbunit($item['file_size']);

        $new_info['is_image'] = $item['is_image'];
        $new_info['image_width'] = $item['image_width'];
        $new_info['image_height'] = $item['image_height'];
        $new_info['image_size_str'] = $item['image_size_str'];

        $new_data[] = $new_info;
    }

    return $new_data;
}


/**
 * 针对数字的加密解密
 * 加密：alpha_id(36731) >> fmQJjeWjk
 * 解密：alpha_id(fmQJjeWjk, true) >> 36731
 *
 * @param string $in 输入字符
 * @param string $to_num 是否转为数字
 * @param number $pad_up 密文长度
 * @param real $pass_key salt
 * @return mixed|string
 */
function alpha_id($in, $to_num = false, $pad_up = 9, $pass_key = 2543.5415412812)
{
    $strbase = "Flpvf70CsakVjqgeWUPXQxSyJizmNH6B1u3b8cAEKwTd54nRtZOMDhoG2YLrI";

    $codelen = substr($strbase, 0, $pad_up);

    $codenums = substr($strbase, $pad_up, 10);

    $codeext = substr($strbase, $pad_up + 10);

    if ($to_num) {
        $begin = substr($in, 0, 1);

        $rtn = '';

        $len = strpos($codelen, $begin);

        if ($len !== false) {
            $len++;

            $arrnums = str_split(substr($in, -$len));

            foreach ($arrnums as $v) {
                $rtn .= strpos($codenums, $v);
            }
        }

        return $rtn;
    } else {
        $rtn = "";

        $numslen = strlen($in);

        // 密文第一位标记数字的长度

        $begin = substr($codelen, $numslen - 1, 1);

        // 密文的扩展位

        $extlen = $pad_up - $numslen - 1;

        $temp = str_replace('.', '', $in / $pass_key);

        $temp = substr($temp, -$extlen);

        $arrextTemp = str_split($codeext);

        $arrext = str_split($temp);

        foreach ($arrext as $v) {
            $rtn .= $arrextTemp[$v];
        }

        $arrnumsTemp = str_split($codenums);

        $arrnums = str_split($in);

        foreach ($arrnums as $v) {
            $rtn .= $arrnumsTemp[$v];
        }

        return $begin . $rtn;
    }
}


function show_webupload_info($input_name, $use_model, $item, $params = array(), $limit = 0, $key = false)
{

    if ($item) {
        if ($limit) {
            //单文件
            if ($key)
                $file_info = get_info($item[$key . '_id']);
            else
                $file_info = get_info($item[$input_name . '_id']);
            if ($file_info)
                $file_info = array($file_info);
        } else {
            //多文件
            $file_info = get_list($item, $use_model);
        }
    }

    $file_info = $file_info ?? "";
    $params['input_name'] = $input_name;
    $params['use_model'] = $use_model;

    //单文件框固定值
    $params['swf_multi'] = false;
    $input_str = '<div class="webupload-con" id="' . $input_name . '"><div class="uploader-list">';
    if ($file_info)
        $num = 0;

    if ($file_info) {
        foreach ($file_info as $v) {
            if ($limit) {
            } else {
                $v = $v->upload;
            }
            $num++;
            $click_id = $input_name . '_info' . $num;
            $input_str .= '<div class="item el-upload-list__item">
                    <span  class="webuploadinfo">' . $v['original_name'] . '</span>
                     <label class="el-upload-list__item-status-label">
                        <i class="fa fa-close webuploadDbtn" ></i>
                    </label>
                    <div class="webuploadinfodiv"><span class="webuploadsize">' . ceil($v['size'] / 1024) . 'K</span>
                    <span class="webuploadstate">已上传</span></div>
            ';
            if ($limit) {
                $input_str .= '<input type="hidden" name="' . $input_name . '" value="' . $v['full_path'] . '">';
                $input_str .= '<input type="hidden" id="' . $click_id . '" data-id="' . $v['alpha_id'] . '" name="' . $input_name . '_id" value="' . $v['alpha_id'] . '">';
            } else {
                $input_str .= '<input type="hidden" id="' . $click_id . '" data-id="' . $v['alpha_id'] . '" name="' . $input_name . '[' . $v['alpha_id'] . ']" value="' . $v['client_name'] . '">';
            }
            $input_str .= '</div>';
        }
    }
    $input_str .= '</div>';
    $input_str .= webUpload_script($params, $limit);

    return $input_str;
}


function webUpload_script($params, $limit = 0)
{
    $webuploader_config = config('webuploader')[$params['config_flag']];
    $str = '<script src="' . asset('/vendor/uploader/js/webuploader.js') . '"></script>';
    $str .= '<script src="' . asset('/vendor/uploader/js/MyWebUploader.js') . '"></script>';
    $str .= '<link rel="stylesheet" href="' . asset('/vendor/uploader/css/webuploader.css') . '"/>';
    $str .= '<link rel="stylesheet" href="' . asset('/vendor/uploader/css/font-awesome.min.css') . '"/>';
    $str .= "<script>
    $(function () {
        powerWebUpload($('#$params[input_name]') ,{
            auto: false,limit:$limit, compress:false,quality:100, name: '$params[input_name]', allowType:'" . implode(' ', $webuploader_config['allow']) . "',
            accept: {
//                title: 'Images',
                extensions: '" . implode(',', $webuploader_config['allow']) . "',
//                mimeTypes: 'image/*'
            },
            datas: {
                use_model: '$params[use_model]',
                show_type: '" . $webuploader_config['show_type'] . "',
                input_name: 'file',
                config_flag: '$params[config_flag]',
            }
        });
    });
</script>";
    return $str;
}
