<?php
return [

    'attach' => [
        'path' => '/attach/',
        'allow' => array(
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
        'path_level' => '{Y}-{m}-{d}',
        'support_crop' => true,
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
        'support_crop' => true,
        'show_type' => 'img'
    ],


];
