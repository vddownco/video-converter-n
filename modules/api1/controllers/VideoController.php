<?php

namespace app\modules\api1\controllers;

use app\components\helpers\VideoFileHelper;
use app\enums\ConsoleCommand;
use app\models\Video;
use app\enums\VideoStatus;
use app\modules\api1\models\ConsoleRunner;
use app\modules\api1\models\FFMpegConverter;
use app\modules\api1\models\Uploader;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class VideoController extends BaseController
{
    const UPLOAD_FILE_PARAMETER = 'file';

    public function actionView( $id )
    {
        $video = Video::findOne( $id );
        $this->checkAccess( $video );
        return $video;
    }

    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName( self::UPLOAD_FILE_PARAMETER );
        if ( $file === null )
        {
            throw new ServerErrorHttpException( 'Please upload a file.' );
        }
        $video = new Video($this->user->id);
        $video->name = $file->name;
        $video->saveName = VideoFileHelper::generateSaveName( $file->name );
        $saveFilePath = $video->getVideoPath();
        $uploader = new Uploader();
        if ( !$uploader->save( $file, $saveFilePath ) )
        {
            throw new ServerErrorHttpException( $uploader->getFirstError() );
        }
        $converter = new FFMpegConverter();
        $info = $converter->getInfo( $saveFilePath );
        $video->setInfo( $info );
        $video->status = VideoStatus::NEED_CONVERT;
        if ( !$video->save() )
        {
            throw new ServerErrorHttpException( $video->getFirstError() );
        }
        (new ConsoleRunner())->run( ConsoleCommand::CONVERT, [ $video->id ] );
        return $video;
    }

    public function actionDownload( $id )
    {
        $video = Video::findOne( $id );
        $this->checkAccess( $video );
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
        $this->checkAccess( $video );
        if ( !$video->delete() )
        {
            throw new ServerErrorHttpException( $video->getFirstError() );
        }
    }

    /**
     * Checks whether the user is the author of the video
     * @param Video $video
     * @throws ForbiddenHttpException
     */
    private function checkAccess( Video $video )
    {
        if ( ( $video === null ) || ( $video->userId != $this->user->id ) )
        {
            throw new ForbiddenHttpException( 'You are not allowed to perform this action.' );
        }
    }
}