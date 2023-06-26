<?php

namespace Mfonte\PHP82DynProps\Classes;

use Mfonte\PHP82DynProps\Traits\SetRandProps;

/**
 * This test is specifically intended to showcase the fact that you *MUST* use the
 * fully-qualified "use \AllowDynamicProperties" in a namespaced context, otherwise the class attribute "#[AllowDynamicProperties]"
 * will not be found by the PHP interpreter.
 *
 * Stack Overflow reference: https://stackoverflow.com/questions/74991682/php-8-2-dynamic-properties-deprecated-how-to-use-them-anyway-in-a-compatible-wa#comment134908738_75039639
 */
#[AllowDynamicProperties]
class StackOverflowUser706420Test
{
    use SetRandProps;

    public function __construct()
    {
        // call the SetRandProps trait from the constructor
        $this->setRandProps();
    }
}
