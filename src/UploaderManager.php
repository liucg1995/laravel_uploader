<?php
namespace Liucg1995\Uploader;

use Illuminate\Http\Request;
use Liucg1995\Uploader\Adapter\BOS;
use Liucg1995\Uploader\Adapter\COS;
use Liucg1995\Uploader\Adapter\Local;
use Liucg1995\Uploader\Adapter\Qiniu;
use Liucg1995\Uploader\Adapter\QOS;
use Liucg1995\Uploader\Adapter\SCS;
use Liucg1995\Uploader\Adapter\Upyun;
use Liucg1995\Uploader\Adapter\OSS;
use Liucg1995\Uploader\Contracts\UploaderContract;
use Exception;

class UploaderManager
{
    private $config;

    private $app;

    private $request;

    private $adapters = [
        'public' =>  Local::class,
        'qiniu'  =>  Qiniu::class,
        'upyun'  =>  Upyun::class,
        'oss'    =>  OSS::class,
        'cos'    =>  COS::class,
        'bos'    =>  BOS::class,
        'scs'    =>  SCS::class
    ];

    public function __construct(Request $request){
        $this->config = config('filesystems');

        $this->app = app();

        $this->request = $request;
    }

    public function extend($key, callable $func){
        $driver = call_user_func($func, $this->app);

        if (!$driver instanceof UploaderContract){
            throw new Exception('The adapter must an instance of '.UploaderContract::class);
        }

        $this->adapters[$key] = $driver;
    }

    /**
     * @param $adapter
     * @throws Exception
     */
    public function setAdapter($adapter){
        if (!$this->supported($adapter)){
            throw new Exception('This adapter is not supported.');
        }

        $this->app->singleton(UploaderContract::class, $this->adapters[$adapter]);
    }

    /**
     *  获取支持的适配器
     * @param null $adapter
     * @return array|bool
     */
    public function supported($adapter = null){
        $supports = array_keys($this->adapters);

        if ($adapter == null){
            return $supports;
        }

        return in_array($adapter, $supports);
    }

    public function build($jsoned = true){

        $adapter = $this->app->make(UploaderContract::class);

        $url = $adapter->url();

        $header = $adapter->header();

        $params = $adapter->params();

        $fileName = $adapter->fileName();

        $responseKey = $adapter->responseKey();

        $res = compact('url', 'header', 'params', 'fileName', 'responseKey');

        return $jsoned ? json_encode($res) : $res;
    }

    public function register(){
        /**
         * 设置适配器
         */
        $default = $this->config['default'];
        if (!$this->supported($default)){
            $default = 'public';
        }
        $this->setAdapter($default);
    }

}