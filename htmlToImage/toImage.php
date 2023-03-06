<?php

use App\Services\OssService;
use Barryvdh\Snappy\Facades\SnappyImage;
use Illuminate\Support\Facades\Log;

public static function createHtmlToImage($filename, $user_id, $data, $view, $type = 1, $dir = 'share/')
{

    try {
        //生成图片
        $path = storage_path($dir);

        if (!is_dir($path)) {
            mkdir($path, 0755, TRUE);
        }

        $public_path = public_path('js');

        $data['path'] = $public_path;

        $save_img = $path . "tmp-$user_id-" . microtime() . ".png";

        SnappyImage::loadView($view, $data)->save($save_img);

        if (!file_exists($save_img)) {
            Log::info('snappy生成图片未成功');
            return false;
        }

        $file = $path . $filename;
        \Intervention\Image\Facades\Image::make($save_img)
            ->resizeCanvas(840, 672, 'top-left')
            ->save($file);

        unlink($save_img);

        if (file_exists($file)) {

            $name = $dir . $filename;

            $client = OssService::getInstance()->uploadDocPhoto($name, $file);

            unlink($file);

            if ($client !== false) {

                if (strpos($client, 'http') !== false) {
                    if (strpos($client, 'https') === false) {
                        $client = str_replace('http', 'https', $client);
                    }
                }

                return ['images' => $client];

            }

        }

        Log::info('生成图片出错');
        return false;

    } catch (\Exception $e) {
        Log::error("上传图片出错：" . $e->getMessage());
        return false;
    }

}
