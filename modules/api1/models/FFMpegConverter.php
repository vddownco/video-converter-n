<?php

namespace app\modules\api1\models;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use yii\base\Model;

class FFMpegConverter extends Model
{
    /**
     * @var FFMpeg
     */
    private $_ffmpeg;

    public function __construct( $config = [] )
    {
        $this->_ffmpeg = FFMpeg::create( $config );
    }

    public function convert( $filePath, $convertFilePath )
    {
        $video = $this->_ffmpeg->open( $filePath );
        $video->save( new X264( 'libmp3lame' ), $convertFilePath );
    }

    public function getInfo( $filePath )
    {
        $streams = $this->_ffmpeg->getFFProbe()->streams( $filePath );
        $videoStream = $streams->videos()->first();
        $audioStream = $streams->audios()->first();
        return new VideoInfo(
            $videoStream->get( 'width' ),
            $videoStream->get( 'height' ),
            $this->convertBirtateToKbps( $videoStream->get( 'bit_rate' ) ),
            $this->convertBirtateToKbps( $audioStream->get( 'bit_rate' ) )
        );
    }

    private function convertBirtateToKbps( $bps )
    {
        return round( $bps / 1024 );
    }
}