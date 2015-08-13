<?php

namespace app\models;

use app\components\helpers\VideoFileHelper;
use app\components\traits\ModelHelperTrait;
use app\modules\api1\models\VideoInfo;
use app\records\VideoRecord;
use Yii;

/**
 * Class Video
 *
 * @inheritdoc
 *
 * @property integer $userId
 * @property integer $originalId
 * @property string $saveName
 * @property integer $videoBitrate
 * @property integer $audioBitrate
 *
 * @method static Video findOne($id)
 *
 * @package app\models
 */
class Video extends VideoRecord
{
    use ModelHelperTrait;

    public function __construct( $userId )
    {
        $this->userId = $userId;
        parent::__construct();
    }

    public static function instantiate($row)
    {
        $userId = isset($row['user_id']) ? $row['user_id'] : null;
        return new static($userId);
    }

    /**
     * Returns the list of fields that should be returned by toArray().
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        // Field 'saveName' used as internal variable and should not return in a response
        unset( $fields[ 'saveName' ] );
        return $fields;
    }

    /**
     * Delete record and file that associated with record
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $filePath = $this->getVideoPath();
        if ( !unlink( $filePath ) )
        {
            $this->addError( 'file', 'File could not be removed' );
            return false;
        }
        if ( !parent::delete() )
        {
            $this->addError( 'file', 'Record could not be deleted' );
            return false;
        }
        return true;
    }

    /**
     * Get video path
     * @return string
     */
    public function getVideoPath()
    {
        return VideoFileHelper::getPath( $this->userId, $this->saveName );
    }

    public function setInfo( VideoInfo $info )
    {
        $this->width = $info->width;
        $this->height = $info->height;
        $this->audioBitrate = $info->audioBitrate;
        $this->videoBitrate = $info->videoBitrate;
    }

    public function saveStatus( $status )
    {
        $this->status = $status;
        return $this->save( true, [ 'status' ] );
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    public function getOriginalId()
    {
        return $this->original_id;
    }

    public function setOriginalId($originalId)
    {
        $this->original_id = $originalId;
    }

    public function getSaveName()
    {
        return $this->save_name;
    }

    public function setSaveName($saveName)
    {
        $this->save_name = $saveName;
    }

    public function getVideoBitrate()
    {
        return $this->video_bitrate;
    }

    public function setVideoBitrate($videoBitrate)
    {
        $this->video_bitrate = $videoBitrate;
    }

    public function getAudioBitrate()
    {
        return $this->audio_bitrate;
    }

    public function setAudioBitrate($audioBitrate)
    {
        $this->audio_bitrate = $audioBitrate;
    }
}
