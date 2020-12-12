<?php
namespace Liucg1995\Uploader\Adapter;
use Liucg1995\Uploader\Contracts\UploaderContract;

class Local implements UploaderContract
{
    private $config;

    public function __construct()
    {
        $this->config = config('filesystems.disks.qiniu');
    }

    public function url() : string {
        return url('Liucg1995/upload');
    }

    public function header() : array {
        return [];
    }

    public function params() : array {
        return [
            'dir' => '{Y}-{m}-{d}'
        ];
    }

    public function fileName() : string {
        return 'file';
    }

    public function responseKey(): string {
        return 'key';
    }

}