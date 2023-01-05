<?php

namespace Mfonte\PHP82DynProps\Classes;

use Mfonte\PHP82DynProps\Utils\Helper;

class MagicSetter
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

    #[AllowDynamicProperties]
    public function __set(string $name, mixed $value)
    {
        $this->{$name} = $value;
    }
}
