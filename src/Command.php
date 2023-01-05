<?php
namespace Mfonte\PHP82DynProps;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;
use splitbrain\phpcli\Colors;
use splitbrain\phpcli\TableFormatter;

class Command extends CLI
{
    private $verbose = false;
    private $command = null;

    private static $availableCommands = [
        'deprecated' => 'Test the "Deprecated" class. This will trigger E_DEPRECATED warnings.',
        'magicSetter' => 'Test the "MagicSetter" class. This will trigger E_DEPRECATED warnings.',
        'badImplementation' => 'Test the "BadImplementation" class. This will trigger E_DEPRECATED warnings.',
        'workingImplementation' => 'Test the "WorkingImplementation" class. This WILL NOT trigger any E_DEPRECATED warnings, thus allowing dynamic properties.',
        'extendedClass' => 'Test the "ExtendedClass", that extends "WorkingImplementation". This WILL NOT trigger any E_DEPRECATED warnings, thus allowing dynamic properties.',
        'usingTrait' => 'Test the "UsingTrait", that uses the "InjectProps" Trait. This WILL NOT trigger any E_DEPRECATED warnings, thus allowing dynamic properties.',
        'all' => 'Test all the classes.',
    ];

    // register options and arguments
    protected function setup(Options $options)
    {
        $options->setHelp("A sample utility to test PHP 8.2 dynamic properties that are set within the class itself.");
        $options->registerOption('verbose', 'verbose output', 'v');

        // register all $availableCommands
        array_walk(self::$availableCommands, function($value, $key) use ($options) { 
            $options->registerCommand($key, $value); 
        });
    }

    // implement your code
    protected function main(Options $options)
    {
        $this->verbose = ($options->getOpt('verbose')) ? true : false;
        $this->command = $options->getCmd();

        // if the command is "all", run all the tests
        if($this->command === 'all') {
            $this->info('Testing all the classes.');

            // run all $availableCommands, but skip the "all" command
            foreach(self::$availableCommands as $command => $description) {
                if($command === 'all') {
                    continue;
                }

                $this->getInfo($command);
                $this->instanceAndTest($command, $options);
            }

            return;
        }

        // based on the context, show an help telling the user what to expect
        $this->getInfo($this->command);

        // instance the class and run the test
        $this->instanceAndTest($this->command, $options);
    }

    /**
     * Instance the class represented by $command and test it
     *
     * @param string $command
     *
     * @return void
     */
    private function instanceAndTest(string $command, Options $options) {
        // instance the class represented by $command and test it
        switch ($command) {
            case 'deprecated':
            case 'magicSetter':
            case 'badImplementation':
            case 'workingImplementation':
            case 'extendedClass':
            case 'usingTrait':
                // init the full class name
                $className = 'Mfonte\\PHP82DynProps\\Classes\\' . ucfirst($command); 
                
                // instance
                $class = new $className();

                // set a dynamic property
                $class->__TESTME = 'some value';

                // dump the object
                if($this->verbose) {
                    $this->dump($class);
                }
                
                break;
            default:
                $this->error('Wrong command.');
                $options->useCompactHelp();
                echo $options->help();
                exit(1);
        }
    }

    /**
     * Show an info message, that represents the command we're testing.
     * Useful to let the developer know what to expect.
     * 
     * @param string $command
     * 
     * @return void
     */
    private function getInfo(string $command) {
        // show the class name we're testing
        $className = ucfirst($command);
        $this->info("Going to test {$className} class.");

        // based on the $command, show an help telling the user what to expect
        switch ($command) {
            case 'deprecated':
                $this->warning("This class is not using any #[AllowDynamicProperties] attribute. This will trigger E_DEPRECATED warnings.");
                break;
            case 'magicSetter':
                $this->warning("This class is using #[AllowDynamicProperties] on the magic __set() method. This is not valid, and will trigger E_DEPRECATED warnings.");
                break;
            case 'badImplementation':
                $this->warning("This class is using #[AllowDynamicProperties] on the class, but the class does not implement the required magic __set() method. Dynamic properties set within the class itself will trigger E_DEPRECATED warnings, but properties set outside of the class will be okay. See @https://php.watch/versions/8.2/dynamic-properties-deprecated#AllowDynamicProperties");
                break;
            case 'workingImplementation':
                $this->warning("This class is using #[AllowDynamicProperties] on the class, and the class implements an empty magic __set() method. This will NOT trigger any E_DEPRECATED warnings, nor for the dynamic properties set within the class, nor for the properties set outside of it. See @https://php.watch/versions/8.2/dynamic-properties-deprecated#__get-__set");
                break;
            case 'extendedClass':
                $this->warning("This class is extending the 'WorkingImplementation' class, and it will NOT trigger any E_DEPRECATED warnings, nor for the dynamic properties set within the class, nor for the properties set outside of it.");
                break;
            case 'usingTrait':
                $this->warning("This class is using the 'InjectProps' trait, and it will NOT trigger any E_DEPRECATED warnings, nor for the dynamic properties set within the class, nor for the properties set outside of it.");
                break;
        }
    }

    /**
     * Dump an object to the console
     * 
     * @param object $object
     * 
     * @return void
     */
    private function dump($object)
    {
        if ($this->verbose) {
            $typeOf = gettype($object);
            $this->info("Dumping {$typeOf} object:");
        }

        $tf = new TableFormatter($this->colors);
        $tf->setBorder(' | '); // nice border between columns

        // show a header
        echo $tf->format(
            ['10%', '10%', '*'],
            ['property', 'type', 'value']
        );

        // a line across the whole width
        echo str_pad('', $tf->getMaxWidth(), '-') . "\n";

        // get Object properties
        $properties = get_object_vars($object);

        foreach ($properties as $key => $val) {
            // get the type of the property
            $type = match (gettype($val)) {
                'boolean' => 'bool',
                'NULL' => 'null',
                'array' => 'array (' . count($val) . ')',
                'object' => 'object (' . get_class($val) . ')',
                default => gettype($val),
            };

            // transform $val into an outputtable string
            $val = match (gettype($val)) {
                'boolean' => ($val) ? 'true' : 'false',
                'NULL' => 'null',
                'array' => implode(',', array_keys($val)),
                'object' => implode(',', get_object_vars($val)),
                default => $val,
            };

            echo $tf->format(
                ['10%', '10%', '*'],
                [$key, $type, $val],
                array(Colors::C_LIGHTGREEN, Colors::C_LIGHTBLUE, Colors::C_LIGHTPURPLE)
            );
        }
    }
}
