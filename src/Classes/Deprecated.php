<?php

namespace Mfonte\PHP82DynProps\Classes;

use Mfonte\PHP82DynProps\Traits\SetRandProps;

class Deprecated
{
    use SetRandProps;

    public function __construct()
    {
        // call the SetRandProps trait from the constructor
        $this->setRandProps();
    }
}
