<?php

namespace Liucg1995\Uploader\Services;

use Hashids\Hashids;
use Illuminate\Filesystem\FilesystemManager;
use Liucg1995\Uploader\Models\Upload;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload
{
    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\FilesystemManager
     */
    protected $filesystem;

    /**
     * Create a new ImageUploadService instance.
     *
     * @param \Illuminate\Filesystem\FilesystemManager $filesystem
     */
    public function __construct(FilesystemManager $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Construct the data URL for the JSON body.
     *
     * @param string $mime
     * @param string $content
     *
     * @return string
     */
    protected function getDataUrl($mime, $content)
    {
        $base = base64_encode($content);

        return 'data:' . $mime . ';base64,' . $base;
    }

    /**
     * Handle the file upload. Returns the response body on success, or false
     * on failure.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param                                                     $disk
     * @param string $dir
     *
     * @return array|bool
     */
    public function store(UploadedFile $file, $disk, $dir = '')
    {
        $hashName = str_ireplace('.jpeg', '.jpg', $file->hashName());

        $dir = $this->formatDir($dir);

        $mime = $file->getMimeType();

        $path = $this->filesystem->disk($disk)->putFileAs($dir, $file, $hashName);

        $upload = new Upload();
        $upload->file_name = $hashName;
        $upload->full_path = $path;
        $upload->mime = $mime;
        $upload->size = $file->getSize();
        $upload->original_name = $file->getClientOriginalName();
        $file_info = pathinfo($file->getClientOriginalName());
        $upload->file_ext = $file_info['extension'];
        $upload->save();
        $upload->alpha_id = Upload::alpha_id($upload->id);
        $upload->save();

        return [
            'success' => true,
            'filename' => $hashName,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $mime,
            'size' => $file->getSize(),
            'key' => $path,
            'alpha_id' =>  $upload->alpha_id ,
            'url' => $this->filesystem->disk($disk)->url($path),
            'dataURL' => $this->getDataUrl($mime, $this->filesystem->disk($disk)->get($path)),
        ];
    }

    /**
     * Replace date variable in dir path.
     *
     * @param string $dir
     *
     * @return string
     */
    protected function formatDir($dir)
    {
        $replacements = [
            '{Y}' => date('Y'),
            '{m}' => date('m'),
            '{d}' => date('d')
        ];

        return str_replace(array_keys($replacements), $replacements, $dir);
    }

    /**
     * Delete a file from disk.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path, $disk)
    {
        if (stripos($path, 'storage') === 0) {
            $path = substr($path, strlen('storage'));
        }

        return $this->filesystem->disk($disk)->delete($path);
    }
}
