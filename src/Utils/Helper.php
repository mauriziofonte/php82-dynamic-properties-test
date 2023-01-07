<?php
namespace Mfonte\PHP82DynProps\Utils;

use Mfonte\PHP82DynProps\Utils\Lorum;

class Helper
{
    /**
     * A simple helper to generate a Collection payload, with randomic types.
     *
     * @param int $min
     * @param int $max
     * @param bool $noArray - if true, the array/object type will not be generated
     *
     * @return array
     */
    public static function generatePayload(int $min = 5, int $max = 20, bool $noArray = false): array
    {
        $payload = [];
        $types = ['string', 'int', 'float', 'bool', 'array', 'object', 'null'];
        $count = rand($min, $max);

        // if noArray is true, remove the array and object types from the types array
        if ($noArray) {
            $types = array_values(array_diff($types, ['array', 'object']));
        }

        $lorum = new Lorum();

        for ($i = 0; $i < $count; $i++) {
            // get a unique key name
            while (true) {
                $key = "_" . $lorum->word(rand(3, 10));
                if (!isset($payload[$key])) {
                    break;
                }
            }
            $type = $types[rand(0, count($types) - 1)];
            $payload[$key] = self::generateValue($type, $min, $max);
        }

        return $payload;
    }

    /**
     * A simple helper to generate a randomic value, based on the type.
     *
     * @param string $type
     * @param int $min - min value to be passed underneath to generatePayload() in case of array/object
     * @param int $max - max value to be passed underneath to generatePayload() in case of array/object
     *
     * @return mixed
     */
    private static function generateValue(string $type, int $min = 5, int $max = 20)
    {
        switch ($type) {
            case 'string':
                $lorum = new Lorum();
                return $lorum->tweet();
            case 'int':
                return rand(0, PHP_INT_MAX);
            case 'float':
                return rand(0, PHP_INT_MAX) / rand(1, PHP_INT_MAX);
            case 'bool':
                return (bool) rand(0, 1);
            case 'array':
                return self::generatePayload(5, 20, true);
            case 'object':
                $array = self::generatePayload(5, 20, true);
                return (object) $array;
            case 'null':
                return null;
        }
    }
}
