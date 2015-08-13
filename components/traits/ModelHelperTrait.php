<?php

namespace app\components\traits;

trait ModelHelperTrait
{
    /**
     * Get first error from model. If set $attribute get first error of the specified attribute.
     * @param null|string $attribute
     * @return null|string
     */
    public function getFirstError( $attribute = null )
    {
        if ( $attribute !== null )
        {
            return parent::getFirstError( $attribute );
        }
        elseif ( $this->hasErrors() )
        {
            $values = array_values( $this->errors );
            return $values[0][0];
        }
        return null;
    }
}