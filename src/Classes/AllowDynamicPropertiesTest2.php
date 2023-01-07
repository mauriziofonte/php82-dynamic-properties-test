<?php

namespace Mfonte\PHP82DynProps\Classes;

use \AllowDynamicProperties;
use Mfonte\PHP82DynProps\Traits\SetRandProps;

#[AllowDynamicProperties]
class AllowDynamicPropertiesTest2
{
    use SetRandProps;

    public ?string $publicProp = null;

    /**
     * Dynamic properties are not automatically set in the constructor, but you can explicitly call the setProps method to set them
     *
     * @return void
     */
    public function setProps()
    {
        // call the SetRandProps trait
        $this->setRandProps();
    }
}
