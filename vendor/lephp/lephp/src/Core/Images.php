<?php
/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/5/17
 * Time: 9:29
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;
class Images
{
    /**
     * 调整图片大小
     * @param $image
     * @param $width
     * @param $height
     * @param $scale
     * @param $SetW
     * @param $SetH
     * @throws Exception
     */
    public static function resizeImage($image, $width, $height, $scale, $SetW, $SetH)
    {
        $imginfo = getimagesize($image);
        if (!$imginfo) {
            throw new Exception('参数错误');
        }
        if ($imginfo['mime'] == "image/pjpeg" || $imginfo['mime'] == "image/jpeg") {
            $source = imagecreatefromjpeg($image);
        } elseif ($imginfo['mime'] == "image/x-png" || $imginfo['mime'] == "image/png") {
            $source = imagecreatefrompng($image);
        } elseif ($imginfo['mime'] == "image/gif") {
            $source = imagecreatefromgif($image);
        }
        if (!$source) {
            throw new Exception('参数错误');
        }
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($SetW, $SetH);
        $color = imagecolorAllocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $color);
        $dx = ($SetW - $newImageWidth) / 2;
        $dy = ($SetH - $newImageHeight) / 2;
        imagecopyresampled($newImage, $source, $dx, $dy, 0, 0, $newImageWidth, $newImageHeight, $width, $height);
        imagejpeg($newImage, $image, 100);
        imagedestroy($newImage);
        chmod($image, 0777);
    }

    /**
     * 裁剪图片
     * @param $img
     * @param $cropx
     * @param $cropy
     * @param $cropw
     * @param $croph
     * @param array $cropedImg
     * @throws Exception
     */
    public static function cropImgBatch($img, $cropx, $cropy, $cropw, $croph, array $cropedImg)
    {
        $source = imagecreatefromjpeg($img);
        if (!$source) {
            throw new Exception('参数错误');
        }
        foreach ($cropedImg as $crop) {
            $newImage = imagecreatetruecolor($crop['width'], $crop['height']);
            imagecopyresampled($newImage, $source, 0, 0, $cropx, $cropy, $crop['width'], $crop['height'], $cropw,
                               $croph);
            imagejpeg($newImage, $crop['file'], 100);
            imagedestroy($newImage);
            chmod($crop['file'], 0777);
        }
    }
}