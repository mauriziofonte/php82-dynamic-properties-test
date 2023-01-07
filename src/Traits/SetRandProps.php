<?php

namespace Mfonte\PHP82DynProps\Traits;

use Mfonte\PHP82DynProps\Utils\Helper;

trait SetRandProps
{
    /**
     * Sets dynamic props into the class, based on a randomic array payload that may contain integers, floats, strings, booleans, arrays and objects
     *
     * @param integer $min
     * @param integer $max
     *
     * @return array
     */
    protected function setRandProps(int $min = 2, int $max = 5) : array
    {
        // generate a randomic payload
        $payload = Helper::generatePayload($min, $max);

        // for each key in the payload, create a dynamic property
        foreach ($payload as $key => $value) {
            $this->{$key} = $value;
        }

        return $payload;
    }
}
