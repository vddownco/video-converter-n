<?php

namespace app\modules\api1\models;

use yii\base\Object;

/**
 * Class VideoInfo
 *
 * @property integer $width
 * @property integer $height
 * @property integer $videoBitrate
 * @property integer $audioBitrate
 *
 * @package app\modules\api1\models
 */
class VideoInfo extends Object
{
    private $_width;
    private $_height;
    private $_videoBitrate;
    private $_audioBitrate;

    public function __construct( $width, $height, $videoBitrate, $audioBitrate )
    {
        $this->_width = $width;
        $this->_height= $height;
        $this->_videoBitrate = $videoBitrate;
        $this->_audioBitrate = $audioBitrate;
    }

    public function getWidth()
    {
        return $this->_width;
    }

    public function getHeight()
    {
        return $this->_height;
    }

    public function getVideoBitrate()
    {
        return $this->_videoBitrate;
    }

    public function getAudioBitrate()
    {
        return $this->_audioBitrate;
    }
}