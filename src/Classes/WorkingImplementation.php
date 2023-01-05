<?php

namespace Mfonte\PHP82DynProps\Classes;

use Mfonte\PHP82DynProps\Utils\Helper;

#[AllowDynamicProperties]
class WorkingImplementation
{
    public function __construct()
    {
        // generate a payload
        $payload = Helper::generatePayload(2, 5);

        // for each key in the payload, create a dynamic property
        foreach ($payload as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Implement the __set() method to be compliant with the RFC.
     *
     * @see https://php.watch/versions/8.2/dynamic-properties-deprecated#__get-__set
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, mixed $value)
    {
        $this->{$name} = $value;
    }
}
