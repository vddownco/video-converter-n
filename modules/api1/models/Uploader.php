<?php

namespace app\modules\api1\models;

use yii\base\Model;
use yii\validators\FileValidator;

/**
 * Class Uploader
 *
 * @property string $filePath
 *
 * @package app\modules\api1\models
 */
class Uploader extends Model
{
    private $_validator;

    public function init()
    {
        $this->_validator = new FileValidator( [ 'skipOnEmpty' => false, 'extensions' => 'flv' ] );
    }

    /**
     * @param \yii\web\UploadedFile $file
     * @param string $saveFilePath
     * @return bool
     */
    public function save ( $file, $saveFilePath )
    {
        $error = null;
        if ( !$this->_validator->validate( $file, $error ) )
        {
            $this->addError( 'file', $error );
            return false;
        }
        if ( !$file->saveAs( $saveFilePath ) )
        {
            $this->addError( 'file', 'File could not be saved' );
            return false;
        }
        return true;
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
}