<?php
// check we're using at least PHP 8.2
if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    echo "\033[01;31mPHP 8.2 or higher is required. You are using " . PHP_VERSION . "\033[0m" . PHP_EOL;
    exit(1);
}

// set the error reporting to ALL
error_reporting(E_ALL);

/**
 * A simple Trait to set dynamic properties into a Class using this trait
 */
trait SetRandProps
{
    /**
     * Sets dynamic props into the class
     *
     * @param integer $min
     * @param integer $max
     *
     * @return array
     */
    protected function setRandProps() : array
    {
        // generate a randomic payload
        $payload = ['_a' => 1, '_b' => 'string', '_c' => 1.1, '_d' => true, '_e' => ['a' => 1, 'b' => 2], '_f' => new stdClass()];

        // for each key in the payload, create a dynamic property
        foreach ($payload as $key => $value) {
            $this->{$key} = $value;
        }

        return $payload;
    }
}

/**
 * This class is not using the #[AllowDynamicProperties] attribute, so it will trigger a deprecation warning.
 * Dynamic properties are set within the constructor
 */
class Deprecated
{
    use SetRandProps;

    public function __construct()
    {
        $this->setRandProps();
    }
}

/**
 * This class is using the #[AllowDynamicProperties] attribute, so it will not trigger a deprecation warning.
 * Dynamic properties are set within the constructor
 */
#[AllowDynamicProperties]
class DynPropsOnConstructor
{
    use SetRandProps;

    public function __construct()
    {
        $this->setRandProps();
    }
}

/**
 * This class extends the DynPropsOnConstructor class, so it will not trigger a deprecation warning.
 * Dynamic properties are set within the constructor
 */
class DynPropsOnConstructorExtended extends DynPropsOnConstructor
{
    public function __construct()
    {
        parent::__construct();
    }
}

/**
 * This class is using the #[AllowDynamicProperties] attribute, so it will not trigger a deprecation warning.
 * Dynamic properties are set by explicitly calling the setProps() method
 */
#[AllowDynamicProperties]
class DynPropsByExplicitCall
{
    use SetRandProps;

    public ?string $publicProp = null;

    public function setProps()
    {
        $this->setRandProps();
    }
}

/**
 * This class extends the DynPropsByExplicitCall class, so it will not trigger a deprecation warning.
 * Dynamic properties are set by explicitly calling the setProps() method
 */
class DynPropsByExplicitCallExtended extends DynPropsByExplicitCall
{
    public function setProps()
    {
        parent::setProps();
        $this->publicProp = 'overridden';
    }
}

// test the deprecated behavior
echo "\033[01;31mDeprecated behavior (will trigger deprecation warnings)\033[0m" . PHP_EOL;
$deprecated = new Deprecated();

// test the new behavior
echo "\033[01;32mNew behavior (will not trigger deprecation warnings)\033[0m" . PHP_EOL;
$dynPropsOnConstructor = new DynPropsOnConstructor();

// test the new behavior, with inheritance
echo "\033[01;32mNew behavior, with inheritance (will not trigger deprecation warnings)\033[0m" . PHP_EOL;
$dynPropsOnConstructorExtended = new DynPropsOnConstructorExtended();

// test the new behavior, with explicit call
echo "\033[01;32mNew behavior, with explicit call (will not trigger deprecation warnings)\033[0m" . PHP_EOL;
$dynPropsByExplicitCall = new DynPropsByExplicitCall();
$dynPropsByExplicitCall->setProps();

// test the new behavior, with explicit call and inheritance
echo "\033[01;32mNew behavior, with explicit call and inheritance (will not trigger deprecation warnings)\033[0m" . PHP_EOL;
$dynPropsByExplicitCallExtended = new DynPropsByExplicitCallExtended();
$dynPropsByExplicitCallExtended->setProps();
