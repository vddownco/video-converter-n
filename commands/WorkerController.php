<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Video;
use app\models\VideoStatus;
use app\modules\api1\models\FFMpegConverter;
use yii\base\ErrorException;
use yii\base\Exception;
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
        $convertingVideoAmount = Video::find()->where( [ 'status' => VideoStatus::CONVERTING ] )->count();
        if ( $convertingVideoAmount >= Video::MAX_CONVERTING_VIDEO_AMOUNT )
        {
            return false;
        }
        return true;
    }

    public function actionConvert( $id = null )
    {
        if ( $id === null )
        {
            $video = Video::find()->where( [ 'status' => VideoStatus::NEED_CONVERT ] )->one();
        }
        else
        {
            $video = Video::findOne( $id );
        }
        $video->status = VideoStatus::CONVERTING;
        if ( !$video->save( false, [ 'status' ] ) )
        {
            throw new ErrorException( $video->getFirstError() );
        }
        $fileName = $video->getVideoName( 'mp4' );
        $convertedVideo = new Video();
        $convertedVideo->userId = $video->userId;
        $convertedVideo->originalId = $video->id;
        $saveFilePath = $convertedVideo->generateSaveFilePath( $fileName );
        $converter = new FFMpegConverter();
        $converter->convert( $video->getVideoPath(), $saveFilePath );
        $info = $converter->getInfo( $saveFilePath );
        $convertedVideo->setInfo( $info );
        $convertedVideo->status = VideoStatus::NO_ACTION;
        if ( !$convertedVideo->save() )
        {
            $video->status = VideoStatus::CONVERSION_ERROR;
            $video->save( false, [ 'status' ] );
            throw new ErrorException( $video->getFirstError() );
        }
        $video->status = VideoStatus::NO_ACTION;
        if ( !$video->save( false, [ 'status' ] ) )
        {
            throw new ErrorException( $video->getFirstError() );
        }
    }
}
