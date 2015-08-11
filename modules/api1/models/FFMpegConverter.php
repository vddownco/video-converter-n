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

    public function convert( $filePath, $convertedFilePath )
    {
        $video = $this->_ffmpeg->open( $filePath );
        return $video->save( new X264( 'libmp3lame' ), $convertedFilePath );
    }

    public function getInfo( $filePath )
    {
        $streams = $this->_ffmpeg->getFFProbe()->streams( $filePath );
        $videoStream = $streams->videos()->first();
        $audioStream = $streams->audios()->first();
        return [
            'width' => $videoStream->get( 'width' ),
            'height' => $videoStream->get( 'height' ),
            'videoBitrate' => round($videoStream->get( 'bit_rate' ) / 1024),
            'audioBitrate' => round($audioStream->get( 'bit_rate' ) / 1024)
        ];
    }
}