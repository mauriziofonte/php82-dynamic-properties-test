<?php

namespace Mfonte\PHP82DynProps\Classes;

class AllowDynamicPropertiesTest2OnExtended extends AllowDynamicPropertiesTest2
{
    /**
     * Overrides the parent setProps method
     *
     * @return void
     */
    public function setProps()
    {
        // call the SetRandProps trait
        $this->setRandProps();

        // override the $publicProp property
        $this->publicProp = 'overridden';
    }
}
