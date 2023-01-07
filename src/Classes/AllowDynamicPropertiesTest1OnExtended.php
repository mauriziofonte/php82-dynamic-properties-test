<?php

namespace Mfonte\PHP82DynProps\Classes;

class AllowDynamicPropertiesTest1OnExtended extends AllowDynamicPropertiesTest1
{
    /**
     * Simply calls the parent constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
}
