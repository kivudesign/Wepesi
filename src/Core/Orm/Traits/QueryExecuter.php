<?php
/**
 * Wepesi ORM
 * QueryExecute
 * Ibrahim Mussa
 * https://github.com/bim-g
 */

namespace Wepesi\Core\Orm\Traits;

use PDO;
use PDOException;

/**
 *
 */
trait QueryExecuter
{
    /**
     * Execute sql request pass by the user
     * @param PDO $pdo pdo object
     * @param string $sql query string
     * @param array $params for prepare request provide params value
     * @param int $last_id in case of loop request provide id of the previous request
     * @param bool $isQuery define if we use the query method directly
     * @return array
     */
    protected function executeQuery(PDO $pdo, string $sql, array $params = [], int $last_id = -1,bool $isQuery = false): array
    {
        try {
            $data_result = [
                'result' => [],
                'lastID' => $last_id,
                'count' => 0,
                'error' => "",
            ];
            $sql_string = explode(' ', strtolower($sql));
            $fetchObject = $isQuery || (isset($this->isCount) && $this->isCount);
            if ($sql_string[0] == 'select' && $fetchObject) {
                $pdo->setAttribute(PDO::ATTR_FETCH_TABLE_NAMES, true);
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
                        if ($fetchObject) {
                            $fetch_result = $query->fetchAll(PDO::FETCH_OBJ);
                            if ( $isQuery ){
                                $data_result['count'] = $query->columnCount();
                            } else {
                                if (isset($fetch_result[0]->{'.count'})) {
                                    array_map(function ($item) {
                                        if ($item->{'.count'}) {
                                            $item->count = $item->{'.count'};
                                            unset($item->{'.count'});
                                        }
                                        return $item;
                                    }, $fetch_result);
                                }
                                $data_result['count'] = $fetch_result[0]->count;
                            }
                            $data_result['result'] = $fetch_result;
                        } else {
                            $data_result['result'] = $query->fetchAll(PDO::FETCH_GROUP);
                            $data_result['count'] = $query->columnCount();
                        }
                        break;
                    case 'insert' :
                        $last_id = (int)$pdo->lastInsertId();
                        $data_result['lastID'] = $last_id;
                        $data_result['count'] = $query->rowCount();
                        $sql = "SELECT * FROM $this->table WHERE id=?";
                        return $this->executeQuery($pdo, $sql, [$last_id], $last_id);
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
        } catch (PDOException $ex) {
            $data_result['error'] = $ex->getmessage();
            return $data_result;
        }
    }
}