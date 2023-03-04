<?php

public function getShareImage(Request $request)
{
    try {
        mb_internal_encoding("UTF-8");

        $params = $request->all();

        $total = $params['total'] ?? 0;
        $bp_normal = $params['bp_normal'] ?? 0;
        $bg_normal = $params['bg_normal'] ?? 0;


        $user_id = $this->getUserInfoBYToken();

        if (empty($user_id)) {
            return $this->error(-20004, "用户错误");
        }

        $app_id = env("C_APPLETS_APP_ID", "");

        if (empty($app_id)) {
            return $this->error(-20005, "APPID缺失");
        }

        //获取姓名
        $user_info = ZWechatUser::getUserInfo($user_id);

        if (!empty($user_info)) {
            $username = !empty($user_info->name) ? $user_info->name : $user_info->nickname;
        }
        $msg = '';

        $bp_normal_msg = '';


        //获取小程序二维码
        $miniProgram = \EasyWeChat::miniProgram();
        //dd($miniProgram);

        $image = $miniProgram->app_code->getUnlimit("user_id=" . $user_id . "&type=shareReport", [
            'width' => '70'
        ]);

        $path = storage_path('wechat_share/');

        $filename = "wechat_share_qrcode" . $user_id . ".png";


        if ($image instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $image->saveAs($path, $filename);
        }

        $save_img = $path . "wechat_share_$user_id.png";


        if (file_exists($path . $filename)) {
            $insert_img = $path . $filename;//二维码的地址

            \Intervention\Image\Facades\Image::make($insert_img)->resize(70, 70)->save($insert_img);//将图片变成合适的大小

            //随机一个数字
            $big_name = mt_rand(1, 25);

            $big_filename = public_path('wechat_share/' . $big_name . '.png');

            $big_filename_save = $path . $big_name . '-' . $user_id . '.png';

            \Intervention\Image\Facades\Image::make($big_filename)->resize(294, 234)->save($big_filename_save);//将图片变成合适的大小

            $rand_num = mt_rand(1, 35);
            $rand_msg = self::SHARE_ARTICLE[$rand_num]['title'];
            $rand_msg_author = self::SHARE_ARTICLE[$rand_num]['author'];
            //背景
            \Intervention\Image\Facades\Image::canvas(294, 394, '#ffffff')->rectangle(0, 294, 0, 394)->insert($insert_img, 'bottom-left', 115, 10)->insert($big_filename_save, 'top')->save($save_img);

            $font = realpath(public_path('/simhei.ttf'));

            $line_wish = 274;
            $font_name_size = 15;
            $font_size = 12;
            //用户名
            $img = imagecreatefrompng($save_img);
            $black = imagecolorallocate($img, 255, 255, 255);

            $diy_wish = $this->autoWrap($font_name_size, 0, $font, $username, $line_wish);
            imagettftext($img, $font_name_size, 0, 20, 60, $black, $font, $this->to_entities($diy_wish));

            $diy_wish_msg = $this->autoWrap($font_size, 0, $font, $msg, $line_wish);
            imagettftext($img, $font_size, 0, 20, 100, $black, $font, $this->to_entities($diy_wish_msg));

            $diy_wish_bp = $this->autoWrap($font_size, 0, $font, $bp_normal_msg, $line_wish);
            imagettftext($img, $font_size, 0, 20, 160, $black, $font, $this->to_entities($diy_wish_bp));

            $diy_wish_bg = $this->autoWrap($font_size, 0, $font, $bg_normal_msg, $line_wish);
            imagettftext($img, $font_size, 0, 20, 190, $black, $font, $this->to_entities($diy_wish_bg));

            $black = imagecolorallocate($img, 0, 0, 0);

            $diy_wish_rand = $this->autoWrap($font_size, 0, $font, $rand_msg, $line_wish);
            imagettftext($img, $font_size, 0, 20, 260, $black, $font, $this->to_entities($diy_wish_rand));

            $diy_wish_author = $this->autoWrap($font_size, 0, $font, $rand_msg_author, $line_wish);
            imagettftext($img, $font_size, 0, 150, 300, $black, $font, $this->to_entities($diy_wish_author));

            //header('Content-Type: image/png');

            ImagePNG($img, $save_img);

            imagedestroy($img);

            /*\Intervention\Image\Facades\Image::make($save_img)
                ->text($username, 10, 10, function ($font) {
                    $font->file(2);
                    $font->size(200);
                    $font->color('#fbfaf6');
                })
                ->text($msg, 10, 80, function ($font) {
                    $font->file(2);
                    $font->size(120);
                    $font->color('#fbfaf6');
                })
                ->text($bp_normal_msg, 10, 100, function ($font) {
                    $font->file(2);
                    $font->size(200);
                    $font->color('#fbfaf6');
                })
                ->text($bg_normal_msg, 10, 110, function ($font) {
                    $font->file(2);
                    $font->size(200);
                    $font->color('#fbfaf6');
                })
                ->text($rand_msg, 10, 800, function ($font) {
                    $font->file(2);
                    $font->size(180);
                })
                ->text($rand_msg_author, 10, 810, function ($font) {
                    $font->file(2);
                    $font->size(180);
                })
                ->save($save_img);*/


            unlink($insert_img);
            unlink($big_filename_save);

            //return $save_img;

            if (file_exists($save_img)) {

                $ossFile = 'wechat_share_qrCode/wechatShare-' . $user_id . '-' . microtime() . '.png';

                $client = OssService::getInstance()->uploadDocPhoto($ossFile, $save_img);

                if ($client !== false) {

                    unlink($save_img);

                    return $this->success(['image' => $client, 'user_id' => $user_id, 'title' => $rand_msg]);

                }
            }

        }

        return $this->error(-20005, '分享错误');

    } catch (\Exception $e) {

        return $this->error(-20006, '分享错误' . $e->getMessage() . $e->getLine());
    }

}


/**
 * 字体换行
 * @param $font_size
 * @param $angle
 * @param $font_face
 * @param $string
 * @param $width
 * @return string
 */
public function autoWrap($font_size, $angle, $font_face, $string, $width)
{
    mb_internal_encoding("UTF-8");
    $content = "";
    $letter = [];
    for ($i = 0; $i < mb_strlen($string); $i++) {
        $letter[] = mb_substr($string, $i, 1);
    }
    foreach ($letter as $l) {
        $test_str = $content . " " . $l;
        $test_box = imagettfbbox($font_size, $angle, $font_face, $this->to_entities($test_str));
        // 判断拼接后的字符串是否超过预设的宽度
        if (($test_box[2] > $width) && ($content !== "")) {
            $content .= "\n";
        }
        $content .= $l;
    }
    return $content;
}


//转换字体 主要是解决中文乱码
public function to_entities($string)
{
    $len = strlen($string);
    $buf = "";
    for ($i = 0; $i < $len; $i++) {
        if (ord($string[$i]) <= 127) {
            $buf .= $string[$i];
        } else if (ord($string[$i]) < 192) {
            //unexpected 2nd, 3rd or 4th byte
            $buf .= "&#xfffd";
        } else if (ord($string[$i]) < 224) {
            //first byte of 2-byte seq
            $buf .= sprintf("&#%d;",
                ((ord($string[$i + 0]) & 31) << 6) +
                (ord($string[$i + 1]) & 63)
            );
            $i += 1;
        } else if (ord($string[$i]) < 240) {
            //first byte of 3-byte seq
            $buf .= sprintf("&#%d;",
                ((ord($string[$i + 0]) & 15) << 12) +
                ((ord($string[$i + 1]) & 63) << 6) +
                (ord($string[$i + 2]) & 63)
            );
            $i += 2;
        } else {
            //first byte of 4-byte seq
            $buf .= sprintf("&#%d;",
                ((ord($string[$i + 0]) & 7) << 18) +
                ((ord($string[$i + 1]) & 63) << 12) +
                ((ord($string[$i + 2]) & 63) << 6) +
                (ord($string[$i + 3]) & 63)
            );
            $i += 3;
        }
    }
    return $buf;
}

