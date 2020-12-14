<?php
return [
    // 文件储存方式
    'disk'=>'public',

    // 文件类型
    'attach' => [
        'path' => '/attach/', // 上传路径
        'allow' => array( // 允许上传的格式
            'gif',
            'jpg',
            'jpeg',
            'png',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'pdf',
            'pptx',
            'rar',
            'zip',
        ),
        'path_level' => '{Y}-{m}-{d}', // 二级路径
        'show_type' => 'attach'
    ],


    'img' => [
        'path' => '/images/',
        'allow' => array(
            'gif',
            'jpg',
            'jpeg',
            'png',
        ),
        'path_level' => '{Y}-{m}-{d}',
        'show_type' => 'img'
    ],


];
