<?php
//使用前期工作

/*
安装wkhtmltopdf
下载 ：wkhtmltopdf => https://wkhtmltopdf.org/downloads.html 并在本地安装

cd wkhtmltox
cp bin/wkhtmltoimage /usr/local/bin/
cp bin/wkhtmltopdf /usr/local/bin/
运行：wkhtmltoimage 指令，没出错即为安装成功

安装laravel-snappy package

composer require barryvdh/laravel-snappy

在 config/app.php 的providers里添加

Barryvdh\Snappy\ServiceProvider::class,

在 config/app.php 的Facade里添加(可选)

'PDF' => Barryvdh\Snappy\Facades\SnappyPdf::class,
'SnappyImage' => Barryvdh\Snappy\Facades\SnappyImage::class,


生成config/snappy.php配置文件

php artisan vendor:publish --provider="Barryvdh\Snappy\ServiceProvider"

修改配置：
'pdf' => [
    'enabled' => true,
    'binary'  => env('WKHTML_PDF_BINARY', base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64')),
    'timeout' => false,
    'options' => [],
    'env'     => [],
],

'image' => [
    'enabled' => true,
    'binary'  => env('WKHTML_IMG_BINARY', base_path('vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64')),
    'timeout' => false,
    'options' => [],
    'env'     => [],
],

用法：
先创建视图：
<img src="{{ $image }}" alt="" class="userHeaderImg">
<div class="userName">{{ $username }}</div>

然后调用视图：
SnappyImage::loadView($view, $data)->save($save_img);




注意：视图中的路径要用绝对路径，图片要用base64位


*/