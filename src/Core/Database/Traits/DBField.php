<?php


namespace Wepesi\Core\Database\Traits;


use Exception;

/**
 * @package Wepesi\Core\Database
 * @template DBField
 */
trait DBField
{
    /**
     * @param array $fields
     * @param string $action
     * @return array
     * @throws Exception
     */
    public function field_params(array $fields, string $action): array
    {
        $action = strtolower($action);
        if (count($fields) && ($action != "insert" || $action != "update")) {
            $keys = $fields;
            $params = $keys;
            $x = 1;
            $keys = array_keys($fields);
            $values = null;
            $_trim_key = [];
            foreach ($fields as $field) {
                $values .= "? ";
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                //remove white space around the column name
                $_trim_key[] = trim($keys[($x - 1)]);
                $x++;
            }
            $keys = $_trim_key;
            $implode_keys = "`" . implode('`,`', $keys) . "`";
            if ($action == "update") {
                $implode_keys = "`" . implode('`= ?,`', $keys) . "`";
                $implode_keys .= "=?";
            }
            return [
                "fields" => $implode_keys,
                "values" => $values,
                "params" => $params
            ];

        } else {
            throw new Exception("This method try to access undefined method");
        }
    }
}