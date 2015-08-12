<?php

namespace app\components\base;

class Model extends \yii\base\Model
{
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