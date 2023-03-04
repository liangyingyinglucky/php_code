<?php
/*
//1.配置图片路径
$src = "1.jpg";
//2.获取图片信息
$info = getimagesize($src);
//3.通过编号获取图像类型
$type = image_type_to_extension($info[2],false);
//4.在内存中创建和图像类型一样的图像
$fun = "imagecreatefrom".$type;
//5.图片复制到内存
$image = $fun($src);

//1.设置字体的路径
$font = "fangzheng.ttf";
//2.填写水印内容
$content = "水印文字:我是好人啊";
//3.设置字体颜色和透明度
$color = imagecolorallocatealpha($image, 255, 255, 255, 0);
//4.写入文字 (图片资源，字体大小，旋转角度，坐标x，坐标y，颜色，字体文件，内容)
imagettftext($image, 30, 0, 1, 60, $color, $font, $content);

//浏览器输出
header("Content-type:".$info['mime']);
$fun = "image".$type;
$fun($image);
//保存图片
$fun($image,'2.'.$type);

imagedestroy($image);
*/

header("Content-Type:text/html; charset=utf-8");
header('Content-type: image/png');// 告诉浏览器，这个文件，是一个png图片
$image = imagecreatefromjpeg("1.jpg");// 创建图像
// 填充颜色 - ps里的点击画布填色
$image_cololr =  imagecolorallocate($image, 149, 188, 205);
imagefill($image, 0, 0,$image_cololr);
$black = imagecolorallocate($image,  105, 105, 105);//文字颜色
imagettftext($image, 21, 0, 70, 220, $black, "fangzheng.ttf", "哈哈哈哈");// 设置中文文字
imagepng($image);// 生成图片
imagedestroy($image);// 销毁图片， 释放内存
