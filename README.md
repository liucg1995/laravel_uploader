
# Laravel Uploader

`Laravel`下的一个上传组件, 支持直传到第三方云存储。

## 安装

```sh
composer require Liucg1995/laravel-uploader
```

## 添加服务提供者

```php
Liucg1995\Uploader\UploaderServiceProvider::class,
```

## 生成资源文件

```sh
php artisan vendor:publish --provider=Liucg1995\\LaravelUploader\\UploadServiceProvider
```

## 设置文件储存方式


config/webuploader.php
```php
[
    'disk'=>'public',
]
```

## 使用

1. 添加上传组件到页面

    单文件上传

    ```php
      {!! form_upload_attach('file','file',$item) !!}
    ```
   多文件上传

   ```php
     {!! form_upload_attaches('file','file','id') !!}
   ```
      
    > 该组件依赖`jQuery`，所以在引入的资源文件的时候必须先引入`jQuery`
                                                                                                                                                                                             
2. 保存附件内容到数据库

    单文件上传  
     
    ```php
    Model::create(['file'=>$request->file , 'file_id'=>$request->file_id]);
    ```
    多文件上传
    
    ```php
     UploadMulti::save_multi_info($request->file, $news->id, 'news');
    ```
   
   
## 直传到云存储
该组件支持直传到第三方云存储，实际上就是模拟了表单上传的方式。从流程上来说相比于传统的先上传到服务器，再从服务器传到云存储来说，少了一步转发。从架构上来说，原来的上传都统一走网站服务器，上传量过大时，瓶颈在网站服务器，可能需要扩容网站服务器。采用表单上传后，上传都是直接从客户端发送到云存储。上传量过大时，压力都在云存储上，由云存储来保障服务质量。

目前支持的第三方云储存：
`本地(local)` `百度云(bos)` `腾讯云(cos)` `阿里云(oss)` `七牛云(qiniu)` `新浪云(scs)` `又拍云(upyun)` 
> 其中的本地不算云存储，只是标识仍旧支持本地磁盘存储。


### 1.配置
百度云：
```php
'disks' => [
    'bos' => [
        'driver'       => 'bos',
        'access_key_id'    =>  'xxxxxxxxxx',
        'access_key_secret'   => 'xxxxxxxxxx',
        'bucket'       => 'xxx',
        'region'    =>  'gz'    //改成存储桶相应地域
    ],
]
```

腾讯云：
```php
'cos' => [
        'driver'       => 'cos',
        'app_id'    =>  '123456789',
        'secret_id'   => 'xxxxxxxxxxx',
        'secret_key'   => 'xxxxxxxxxxx',
        'bucket'       => 'xxxxxxxxx',
        'region'    =>  'sh'    //改成存储桶相应地域
    ]
```
> 注意，腾讯云存储的时候不是以资源的访问路径存的，会加上appid和存储桶的参数。主要是腾讯云上传后没有返回资源的相对路径，而且这样的存储方式也是官方推崇的。

阿里云：
```php
'oss' => [
        'driver'       => 'oss',
        'access_key'   => 'xxxxxxxxxx',
        'secret_key'   => 'xxxxxxxxxx',
        'bucket'       => 'xxxxx',
    ],
```
```php
composer require "iidestiny/laravel-filesystem-oss" 
```

七牛云：
```php
'qiniu' => [
        'driver'     => 'qiniu',
        'access_key' => 'xxxxxxxxxxxxxxxxxx',
        'secret_key' => 'xxxxxxxxxxxxxxxxxx',
        'bucket'     => 'xxxxxxxxxxxxxxxxxx',
        'domain'     => 'xxxxxxxxxxx'
    ],
```
```php
composer require "zgldh/qiniu-laravel-storage"
```



### 2.设置云储存

config/webuploader.php
```php
[
    'disk'=>'qiniu',
]
```

## License
MIT

