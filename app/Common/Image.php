<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *   http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Common;

class Image{

    /**
     * 图片高斯模糊（适用于png/jpg/gif格式）
     * @param $srcImg 原图片
     * @param $savepath 保存路径
     * @param $savename 保存名字
     * @param $positon 模糊程度
     *
     *基于Martijn Frazer代码的扩充， 感谢 Martijn Frazer
     */
    public function gaussianBlur($srcImg,$savepath=null,$savename=null,$blurFactor=3){

        $gdImageResource=$this->image_create_from_ext($srcImg);
        $srcImgObj=$this->blur($gdImageResource,$blurFactor);
        $temp = pathinfo($srcImg);
        $name = $temp['basename'];
        $path = $temp['dirname'];
        $exte = $temp['extension'];
        $savename = $savename ? $savename : $name;
        $savepath = $savepath ? $savepath : $path;
        $savefile = $savepath .'/'. $savename;
        $srcinfo = @getimagesize($srcImg);

        switch ($srcinfo[2]) {
            case 1: imagegif($srcImgObj, $savefile); break;
            case 2: imagejpeg($srcImgObj, $savefile); break;
            case 3: imagepng($srcImgObj, $savefile); break;
            default: return '保存失败'; //保存失败
        }
        return $savefile;
        imagedestroy($srcImgObj);
    }

    /**
     * Strong Blur
     *
     * @param  $gdImageResource  图片资源
     * @param  $blurFactor          可选择的模糊程度
     *  可选择的模糊程度  0使用   3默认   超过5时 极其模糊
     * @return GD image 图片资源类型
     * @author Martijn Frazer, idea based on http://stackoverflow.com/a/20264482
     */
    private function blur($gdImageResource, $blurFactor = 3)
    {
        // blurFactor has to be an integer
        $blurFactor = round($blurFactor);

        $originalWidth = imagesx($gdImageResource);
        $originalHeight = imagesy($gdImageResource);

        $smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
        $smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));

        // for the first run, the previous image is the original input
        $prevImage = $gdImageResource;
        $prevWidth = $originalWidth;
        $prevHeight = $originalHeight;

        // scale way down and gradually scale back up, blurring all the way
        for($i = 0; $i < $blurFactor; $i += 1)
        {
            // determine dimensions of next image
            $nextWidth = $smallestWidth * pow(2, $i);
            $nextHeight = $smallestHeight * pow(2, $i);

            // resize previous image to next size
            $nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
            imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
                $nextWidth, $nextHeight, $prevWidth, $prevHeight);

            // apply blur filter
            imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);

            // now the new image becomes the previous image for the next step
            $prevImage = $nextImage;
            $prevWidth = $nextWidth;
            $prevHeight = $nextHeight;
        }

        // scale back to original size and blur one more time
        imagecopyresized($gdImageResource, $nextImage,
            0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
        imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);

        // clean up
        imagedestroy($prevImage);

        // return result
        return $gdImageResource;
    }

    private function image_create_from_ext($imgfile)
    {
        $info = getimagesize($imgfile);
        $im = null;
        switch ($info[2]) {
            case 1: $im=imagecreatefromgif($imgfile); break;
            case 2: $im=imagecreatefromjpeg($imgfile); break;
            case 3: $im=imagecreatefrompng($imgfile); break;
        }
        return $im;
    }

}
