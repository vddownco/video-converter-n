<?php

namespace app\models;

use app\records\VideoRecord;
use Yii;

class Video extends VideoRecord
{
    const MAX_CONVERTING_VIDEO_AMOUNT = 5;

    public function fields()
    {
        $fields = parent::fields();
        unset( $fields[ 'saveName' ] );
        return $fields;
    }

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

    public function generateSaveFilePath( $fileName )
    {
        $this->name = $fileName;
        $baseName = pathinfo( $fileName, PATHINFO_FILENAME );
        $extension = pathinfo( $fileName, PATHINFO_EXTENSION );
        $this->saveName = $baseName . '.' . time() . '.' . $extension;
        return $this->getFilePath( $this->saveName );
    }

    public function getFirstError( $attribute = null )
    {
        if ( $attribute !== null )
        {
            return parent::getFirstError( $attribute );
        }
        elseif ( isset( $this->errors ) )
        {
            $values = array_values( $this->errors );
            return $values[0][0];
        }
        return null;
    }

    public function getVideoPath()
    {
        return $this->getFilePath( $this->saveName );
    }

    public function getVideoName()
    {
        return pathinfo( $this->name, PATHINFO_FILENAME );
    }

    public function getFilePath( $name )
    {
        return strtr( '{webroot}/content/{userId}/{name}', [
            '{webroot}' => \Yii::$app->basePath.'/web',
            '{userId}' => $this->userId,
            '{name}' => $name,
        ]);
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
