<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

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
            if ( $convertingVideoAmount >= Video::MAX_CONVERTING_VIDEO_AMOUNT )
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
        }
        else
        {
            $video = Video::findOne( $id );
        }
        if ( $video === null )
        {
            return;
        }
        if ( !$video->saveStatus( VideoStatus::CONVERTING ) )
        {
            throw new ErrorException( $video->getFirstError() );
        }
        $convertVideoFileName = $video->getVideoName( 'mp4' );
        $convertVideo = new Video( $video->userId );
        $convertVideo->originalId = $video->id;
        $saveFilePath = $convertVideo->generateSaveFilePath( $convertVideoFileName );
        $converter = new FFMpegConverter();
        $converter->convert( $video->getVideoPath(), $saveFilePath );
        $info = $converter->getInfo( $saveFilePath );
        $convertVideo->setInfo( $info );
        $convertVideo->status = VideoStatus::NO_ACTION;
        if ( !$convertVideo->save() )
        {
            $video->saveStatus( VideoStatus::CONVERTING_ERROR );
            throw new ErrorException( $video->getFirstError() );
        }
        if ( $video->saveStatus( VideoStatus::NO_ACTION ) )
        {
            throw new ErrorException( $video->getFirstError() );
        }
    }
}
