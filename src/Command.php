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
        'deprecated' => [
            'description' => 'Test the "Deprecated" class. This will trigger E_DEPRECATED warnings.',
            'expect' => 'This class is not using any #[AllowDynamicProperties] attribute. This will trigger E_DEPRECATED warnings.',
            'call' => null
        ],
        'allowDynamicPropertiesTest1' => [
            'description' => 'Test the "AllowDynamicPropertiesTest1" class. This should not trigger any E_DEPRECATED warning.',
            'expect' => 'This class is using the #[AllowDynamicProperties] attribute. Dynamic properties are set in the constructor.',
            'call' => null
        ],
        'allowDynamicPropertiesTest1OnExtended' => [
            'description' => 'Test the "AllowDynamicPropertiesTest1OnExtended" class. This should not trigger any E_DEPRECATED warning.',
            'expect' => 'This class is extending the "AllowDynamicPropertiesTest1" class, which is using the #[AllowDynamicProperties] attribute. Dynamic properties are set in the constructor.',
            'call' => null
        ],
        'allowDynamicPropertiesTest2' => [
            'description' => 'Test the "AllowDynamicPropertiesTest2" class. This should not trigger any E_DEPRECATED warning.',
            'expect' => 'This class is using the #[AllowDynamicProperties] attribute. Dynamic properties are not automatically set in the constructor, but you can explicitly call the setProps method to set them.',
            'call' => 'setProps'
        ],
        'allowDynamicPropertiesTest2OnExtended' => [
            'description' => 'Test the "AllowDynamicPropertiesTest2OnExtended" class. This should not trigger any E_DEPRECATED warning.',
            'expect' => 'This class is extending the "AllowDynamicPropertiesTest2" class, which is using the #[AllowDynamicProperties] attribute. Dynamic properties are not automatically set in the constructor, but you can explicitly call the setProps method to set them.',
            'call' => 'setProps'
        ],
        // StackOverflowUser706420Test
        'stackOverflowUser706420Test' => [
            'description' => 'Test the "StackOverflowUser706420Test" class. This should not trigger any E_DEPRECATED warning.',
            'expect' => 'This class is using the #[AllowDynamicProperties] attribute, but is not using the fully-qualified "use \AllowDynamicProperties;" statement, though we are on a namespaced context, and that "use" statement is indeed needed. This test will trigger E_DEPRECATED warnings. This test is intended to let the community know that the comment https://stackoverflow.com/questions/74991682/php-8-2-dynamic-properties-deprecated-how-to-use-them-anyway-in-a-compatible-wa#comment134908738_75039639 is not consistent.',
            'call' => null
        ],
        'all' => [
            'description' => 'Test all the classes.',
            'expect' => 'This will run all the tests.'
        ]
    ];

    // register options and arguments
    protected function setup(Options $options)
    {
        $options->setHelp('A sample utility to test PHP 8.2 Dynamic Properties on Classes that set these within the class constructor itself, or classes that implement another method that sets dynamic properties.');
        $options->registerOption('verbose', 'verbose output', 'v');

        // register all $availableCommands
        array_walk(self::$availableCommands, function ($value, $key) use ($options) {
            $options->registerCommand($key, $value['description']);
        });
    }

    // implement your code
    protected function main(Options $options)
    {
        $this->verbose = ($options->getOpt('verbose')) ? true : false;
        $this->command = $options->getCmd();

        // if the command is "all", run all the tests
        if ($this->command === 'all') {
            $this->info('Testing all the classes.');

            // run all $availableCommands, but skip the "all" command
            foreach (self::$availableCommands as $command => $description) {
                if ($command === 'all') {
                    continue;
                }

                $this->getExpectInfo($command);
                $this->instanceAndTest($command, $options);
            }

            return;
        }

        // based on the context, show an help telling the user what to expect
        $this->getExpectInfo($this->command);

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
    private function instanceAndTest(string $command, Options $options)
    {
        // instance the class represented by $command and test it
        switch ($command) {
            case 'deprecated':
            case 'allowDynamicPropertiesTest1':
            case 'allowDynamicPropertiesTest1OnExtended':
            case 'allowDynamicPropertiesTest2':
            case 'allowDynamicPropertiesTest2OnExtended':
            case 'stackOverflowUser706420Test':
                // init the full class name
                $className = 'Mfonte\\PHP82DynProps\\Classes\\' . ucfirst($command);
                
                // instance
                $class = new $className();

                // set a dynamic property
                $class->runtimeDynProp = 'some value';

                // check if we have to explicitly call a method to set dynamic properties
                if (self::$availableCommands[$command]['call']) {
                    $method = self::$availableCommands[$command]['call'];
                    $class->$method();
                }

                // dump the object
                if ($this->verbose) {
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
    private function getExpectInfo(string $command)
    {
        // show the class name we're testing
        $className = ucfirst($command);
        $this->info("Going to test {$className} class.");

        // print the "expect" key on $availableCommands[$command]
        $this->warning(self::$availableCommands[$command]['expect']);
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
