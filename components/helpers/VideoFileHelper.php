<?php

namespace app\components\helpers;

class VideoFileHelper
{
    /**
     * Get video file path
     * @param integer $userId id author videos
     * @param string $videoName Video name with extension
     * @return string
     */
    public static function getPath( $userId, $videoName )
    {
        return strtr( '{webroot}/content/{userId}/{name}', [
            '{webroot}' => \Yii::$app->basePath . '/web',
            '{userId}' => $userId,
            '{name}' => $videoName
        ]);
    }

    /**
     * Generate save path for video with name $fileName.
     * Set name and saveName record attributes.
     * @param $videoName
     * @return string
     */
    public static function generateSaveName( $videoName )
    {
        $baseName = pathinfo( $videoName, PATHINFO_FILENAME );
        $extension = pathinfo( $videoName, PATHINFO_EXTENSION );
        return $baseName . '.' . time() . '.' . $extension;
    }

    public static function changeExtension( $fileName, $extention )
    {
        $baseName = pathinfo( $fileName, PATHINFO_FILENAME );
        return $baseName . '.' . $extention;
    }
}