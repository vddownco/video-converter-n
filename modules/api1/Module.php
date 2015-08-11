<?php

namespace app\modules\api1;

use yii\web\Response;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\api1\controllers';

    public function init()
    {
        parent::init();
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }
}
