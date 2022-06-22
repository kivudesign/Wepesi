<?php

namespace Wepesi\Core\Orm;
use PDO;

class DBQuery
{
    private PDO $_pdo;
    private $result;
    private string $error;
    private int $rowCount,$lastInsertId;

    function __construct(\PDO $pdo, string $sql, array $param = [])
    {
        $this->_pdo = $pdo;
        $this->lastInsertId = 0;
        $this->rowCount = 0;
        $this->executeQuery($sql, $param);
        $this->error = false;
//        $this->result=[];
    }
    private function executeQuery($sql, array $params = [])
    {
        try {
            $query = $this->_pdo->prepare($sql);
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $query->bindValue($x, $param);
                    $x++;
                }
            }
            $query->execute();

            if (strchr(strtolower($sql), "update") || strchr(strtolower($sql), "select")) {
                $this->result = $query->fetchAll(PDO::FETCH_OBJ);
                $this->rowCount = $query->rowCount();
            } else if (strchr(strtolower($sql), "insert into")) {
                $this->lastInsertId = $this->_pdo->lastInsertId();
//                return array_merge([$this->lastInsertId],$params);
            }else{
                $this->result=true;
            }
        } catch (\Exception $ex) {
            $this->error = $ex->getmessage();
        }
    }
    function rowCount()
    {
        return $this->rowCount;
    }
    function lastid(): int
    {
        return $this->lastInsertId;
    }
    function result()
    {
        return $this->result;
    }
    function getError()
    {
        return $this->error;
    }
}
