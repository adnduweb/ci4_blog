<?php

namespace Adnduweb\Ci4_blog\Traits;

trait PostTrait
{
    public function getType()
    {
        return ['1' => 'publied', '2' => 'wait corrected', '3' => 'wait publied', '4' => 'brouillon'];
    }
}
