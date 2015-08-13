<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\helpers\VideoFileHelper;
use app\models\Video;
use app\enums\VideoStatus;
use app\modules\api1\models\FFMpegConverter;
use yii\base\ErrorException;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class WorkerController extends Controller
{
    public function beforeAction( $action )
    {
        if ( !parent::beforeAction( $action ) )
        {
            return false;
        }
        if ( $action->id == 'convert' )
        {
            $convertingVideoAmount = Video::find()->where( [ 'status' => VideoStatus::CONVERTING ] )->count();
            if ( $convertingVideoAmount >= \Yii::$app->params[ 'max_converting_video_amount' ] )
            {
                return false;
            }
        }
        return true;
    }

    public function actionConvert( $id = null )
    {
        if ( $id === null )
        {
            $video = Video::find()->where( [ 'status' => VideoStatus::NEED_CONVERT ] )->one();
            if ( $video === null )
            {
                return Controller::EXIT_CODE_NORMAL;
            }
        }
        else
        {
            $video = Video::findOne( $id );
            if ( $video === null )
            {
                $this->handleError( "Video with id = '$id' not found." );
                return Controller::EXIT_CODE_ERROR;
            }
        }
        if ( !$video->saveStatus( VideoStatus::CONVERTING ) )
        {
            throw new ErrorException( $video->getFirstError() );
        }
        $convertVideoName = VideoFileHelper::changeExtension( $video->name, 'mp4' );
        $convertVideo = new Video( $video->userId );
        $convertVideo->originalId = $video->id;
        $convertVideo->name = $convertVideoName;
        $convertVideo->saveName = VideoFileHelper::generateSaveName( $convertVideoName );
        $saveFilePath = $convertVideo->getVideoPath();
        $converter = new FFMpegConverter();
        if ( !$converter->convertToMp4( $video->getVideoPath(), $saveFilePath ) )
        {
            $this->handleError( $converter->getFirstError() );
            return Controller::EXIT_CODE_ERROR;
        }
        $info = $converter->getInfo( $saveFilePath );
        $convertVideo->setInfo( $info );
        $convertVideo->status = VideoStatus::NO_ACTION;
        if ( !$convertVideo->save() )
        {
            $this->handleError( $converter->getFirstError() );
            return Controller::EXIT_CODE_ERROR;
        }
        if ( $video->saveStatus( VideoStatus::NO_ACTION ) )
        {
            $this->handleError( $converter->getFirstError() );
            return Controller::EXIT_CODE_ERROR;
        }
        return Controller::EXIT_CODE_NORMAL;
    }

    private function handleError( $error )
    {
        echo 'Error: ' . $error;
        \Yii::error( $error );
    }
}
