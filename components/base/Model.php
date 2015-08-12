<?php

namespace app\components\base;

class Model extends \yii\base\Model
{
    /**
     * Get first error from model or first error of the specified attribute.
     * @param null|string $attribute
     * @return null|string
     */
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