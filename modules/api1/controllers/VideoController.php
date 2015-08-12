<?php

namespace app\modules\api1\controllers;

use app\models\Video;
use app\models\VideoStatus;
use app\modules\api1\models\ConsoleRunner;
use app\modules\api1\models\FFMpegConverter;
use app\modules\api1\models\Uploader;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class VideoController extends BaseController
{
    public function actionView( $id )
    {
        $video = Video::findOne( $id );
        $this->checkAccess( $this->action, $video );
        return $video;
    }

    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName( 'file' );
        $video = new Video();
        $video->userId = $this->user->id;
        $saveFilePath = $video->generateSaveFilePath( $file->name );
        (new Uploader())->save( $file, $saveFilePath );
        $info = (new FFMpegConverter())->getInfo( $saveFilePath );
        $video->attributes = [
            'width' => $info[ 'width' ],
            'height' => $info[ 'height' ],
            'audioBitrate' => $info[ 'audioBitrate' ],
            'videoBitrate' => $info[ 'videoBitrate' ],
            'status' => VideoStatus::NEED_CONVERT
        ];
        if ( !$video->save() )
        {
            throw new ServerErrorHttpException( $video->getFirstError() );
        }
        (new ConsoleRunner())->run( 'worker/convert ' . $video->id );
        return $video;
    }

    public function actionDownload( $id )
    {
        $video = Video::findOne( $id );
        $this->checkAccess( $this->action, $video );
        \Yii::$app->response->sendFile( $video->getVideoPath(), $video->name );
    }

    public function actionList()
    {
        $userId = \Yii::$app->user->identity->getId();
        return Video::find()->where( [ 'user_id' => $userId ] )->all();
    }

    public function actionDelete( $id )
    {
        $video = Video::findOne( $id );
        $this->checkAccess( $this->action, $video );
        if ( !$video->delete() )
        {
            throw new ServerErrorHttpException( $video->getFirstError() );
        }
    }

    public function checkAccess( $action, $model, $params = [] )
    {
        if ( ( $model === null ) || ( $model->userId != $this->user->id ) )
        {
            throw new ForbiddenHttpException( 'You are not allowed to perform this action.' );
        }
    }
}