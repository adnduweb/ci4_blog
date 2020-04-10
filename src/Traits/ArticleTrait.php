<?php

namespace Spreadaurora\Ci4_blog\Traits;

trait ArticleTrait
{
    public function getType()
    {
        return ['1' => 'publied', '2' => 'wait corrected', '3' => 'wait publied', '4' => 'brouillon'];
    }
}
