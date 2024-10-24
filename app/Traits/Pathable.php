<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Traits;


trait Pathable
{
    public function getAttributeValue($value)
    {
        if (in_array($value, $this->pathAttribute)) {
            return is_null(parent::getAttributeValue($value)) ? null:asset(parent::getAttributeValue($value));
        }
        return parent::getAttributeValue($value);
    }
}
