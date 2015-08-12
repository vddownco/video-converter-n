<?php

namespace app\modules\api1\controllers;

use app\models\User;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\RateLimiter;
use yii\web\Controller;

class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    public $user;

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBasicAuth::className(),
                'auth' => [ $this, 'getUser' ]
            ],
            'rateLimiter' => [
                'class' => RateLimiter::className(),
            ],
        ];
    }

    public function beforeAction( $action )
    {
        if ( !parent::beforeAction( $action ) )
        {
            return false;
        }
        $this->user = \Yii::$app->user->identity;
        return true;
    }

    public function getUser( $username, $password )
    {
        $user = User::findOne( [ 'username' => $username ] );
        if ( ( $user === null ) || !$user->isValidPassword( $password ) ) {
            return null;
        }
        return $user;
    }
}