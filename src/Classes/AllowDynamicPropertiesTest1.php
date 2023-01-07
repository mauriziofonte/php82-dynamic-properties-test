<?php

namespace Mfonte\PHP82DynProps\Classes;

use Mfonte\PHP82DynProps\Traits\SetRandProps;

#[AllowDynamicProperties]
class AllowDynamicPropertiesTest1
{
    use SetRandProps;

    public ?string $publicProp = null;

    /**
     * Dynamic properties are set in the constructor, using the SetRandProps trait
     *
     * @return void
     */
    public function __construct()
    {
        // call the SetRandProps trait from the constructor
        $this->setRandProps();
    }
}
