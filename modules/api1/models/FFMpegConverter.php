<?php

namespace app\modules\api1\models;

use app\components\traits\ModelHelperTrait;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use yii\base\Model;

class FFMpegConverter extends Model
{
    use ModelHelperTrait;

    const AUDIO_CODEC = 'libmp3lame';

    /**
     * @var FFMpeg
     */
    private $_ffmpeg;

    public function __construct( $config = [] )
    {
        $this->_ffmpeg = FFMpeg::create( $config );
    }

    /**
     * Convert video to mp4 and save in $convertFilePath
     * @param $filePath
     * @param $convertFilePath
     * @return bool
     */
    public function convertToMp4( $filePath, $convertFilePath )
    {
        try
        {
            $video = $this->_ffmpeg->open( $filePath );
            $video->save( new X264( self::AUDIO_CODEC ), $convertFilePath );
            return true;
        }
        catch (\Exception $e)
        {
            $this->addError( 'video', $e->getMessage() );
            return false;
        }
    }

    /**
     * Get information about file
     * @param $filePath
     * @return VideoInfo
     */
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