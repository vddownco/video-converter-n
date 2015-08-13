<?php

namespace app\models;

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
     * Generate save path for video with name $fileName.
     * Set name and saveName record attributes.
     * @param $fileName
     * @return string
     */
    public function generateSaveFilePath( $fileName )
    {
        $this->name = $fileName;
        $baseName = pathinfo( $fileName, PATHINFO_FILENAME );
        $extension = pathinfo( $fileName, PATHINFO_EXTENSION );
        $this->saveName = $baseName . '.' . time() . '.' . $extension;
        return $this->getFilePath( $this->saveName );
    }

    /**
     * Get video path
     * @return string
     */
    public function getVideoPath()
    {
        return $this->getFilePath( $this->saveName );
    }

    /**
     * Get video file name without extension.
     * If set $withExtension return file name with extension $withExtension.
     * @param string|null $withExtension
     * @return mixed|string
     */
    public function getVideoName($withExtension = null)
    {
        $name = pathinfo( $this->name, PATHINFO_FILENAME );
        if ( $withExtension !== null)
        {
            $name .= '.' . $withExtension;
        }
        return $name;
    }

    /**
     * Get file path with name $namme
     * @param string $name File name with extension
     * @return string
     */
    public function getFilePath( $name )
    {
        return strtr( '{webroot}/content/{userId}/{name}', [
            '{webroot}' => \Yii::$app->basePath . '/web',
            '{userId}' => $this->userId,
            '{name}' => $name,
        ]);
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
