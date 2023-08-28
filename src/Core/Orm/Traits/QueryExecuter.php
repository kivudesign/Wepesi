<?php
/**
 * Wepesi ORM
 * QueryExecute
 * Ibrahim Mussa
 * https://github.com/bim-g
 */

namespace Wepesi\Core\Orm\Traits;

/**
 *
 */
trait QueryExecuter
{
    /**
     * @param \PDO $pdo
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function executeQuery(\PDO $pdo, string $sql, array $params = []): array
    {
        try {
            $data_result = [
                'result' => [],
                'lastID' => -1,
                'count' => 0,
                'error' => "",
            ];
            $sql_string = explode(' ', strtolower($sql));
            if ($sql_string[0] == 'select' && isset($this->include_object) && count($this->include_object) > 0) {
                $pdo->setAttribute(\PDO::ATTR_FETCH_TABLE_NAMES, true);
            }

            $query = $pdo->prepare($sql);
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $query->bindValue($x, $param);
                    $x++;
                }
            }
            $query_result = $query->execute();

            if ($query_result) {
                $data_result['result'] = ['query_result' => true];

                switch ($sql_string[0]) {
                    case 'select' :
                        if (count($this->include_object) > 0) {
                            $data_result['result'] = $query->fetchAll(\PDO::FETCH_GROUP);
                        } else {
                            $data_result['result'] = $query->fetchAll(\PDO::FETCH_OBJ);
                        }
                        $data_result['count'] = $query->columnCount();
                        break;
                    case 'insert' :
                        $last_id = $pdo->lastInsertId();
                        $data_result['lastID'] = $last_id;
                        $data_result['count'] = $query->rowCount();
                        $sql = "SELECT * FROM $this->table WHERE id=?";
                        return $this->executeQuery($pdo, $sql, [$last_id]);
                        break;
                    case 'update':
                        $data_result['count'] = $query->rowCount();
                        break;
                    case 'delete':
                        $data_result['result'] = ['delete' => $query->rowCount() > 0];
                        $data_result['count'] = $query->rowCount();
                        break;
                }
            }
            return $data_result;
        } catch (\PDOException $ex) {
            $data_result['error'] = $ex->getmessage();
            return $data_result;
        }
    }
}