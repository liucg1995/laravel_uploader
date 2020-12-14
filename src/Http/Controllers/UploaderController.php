<?php
namespace Liucg1995\Uploader\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Liucg1995\Uploader\Services\FileUpload;

class UploaderController extends BaseController
{
    public function upload(Request $request, FileUpload $fileUpload){
        $show_type = $request->show_type;
        $webuploader_config = config('webuploader')[$request->config_flag];
        $inputName = 'file';
        $directory = $webuploader_config['path'].$webuploader_config['path_level'];
        $disk = config('filesystems.default', 'public');
        if (!$request->hasFile($inputName)) {
            return [
                'success' => false,
                'error' => 'no file found.',
            ];
        }
        $file = $request->file($inputName);

        return $fileUpload->store($file, $disk, $directory);
    }

    public function delete(Request $request)
    {
        $result = ['result' => app(FileUpload::class)->delete($request->get('file'))];

        return $result;
    }
}
