<?php

namespace app\records;

use app\models\User;
use app\models\Video;
use Yii;

/**
 * This is the model class for table "video".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $original_id
 * @property string $name
 * @property string $save_name
 * @property integer $width
 * @property integer $height
 * @property integer $video_bitrate
 * @property integer $audio_bitrate
 * @property integer $status
 *
 * @property User $user
 * @property VideoRecord $original
 * @property VideoRecord[] $videoRecords
 */
class VideoRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'name', 'save_name'], 'required'],
            [['user_id', 'original_id', 'width', 'height', 'video_bitrate', 'audio_bitrate', 'status'], 'integer'],
            [['name', 'save_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'original_id' => 'Original ID',
            'name' => 'Name',
            'save_name' => 'Save Name',
            'width' => 'Width',
            'height' => 'Height',
            'video_bitrate' => 'Video Bitrate',
            'audio_bitrate' => 'Audio Bitrate',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOriginal()
    {
        return $this->hasOne(Video::className(), ['id' => 'original_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVideoRecords()
    {
        return $this->hasMany(Video::className(), ['original_id' => 'id']);
    }
}
