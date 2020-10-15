<?php

namespace Moon\Tools;

class CompareDatabases
{
    protected $db1;
    protected $db2;

    public function __construct(\PDO $db1, \PDO $db2)
    {
        $this->db1 = $db1;
        $this->db2 = $db2;
    }

    public function compare(array $tables = [])
    {
        $tables1 = static::showTables($this->db1);
        //$tables2 = static::showTables($this->db2);
        if (empty($tables)) {
            $tables = $tables1;
        }

        $dst = [];

        foreach ($tables as $table) {
            $tableInfo1 = static::getTableStructInfo($this->db1, $table);
            $tableInfo2 = static::getTableStructInfo($this->db2, $table);

            if ($tableInfo1 !== $tableInfo2) {
                $dst[$table] = [$tableInfo1, $tableInfo2];
            }
        }
        return $dst;
    }

    public static function showTables(\PDO $db)
    {
        return $db->query("show tables")->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function getTableStructInfo(\PDO $db, $tableName, $asArray = false)
    {
        try {
            $res = $db->query("show create table `$tableName`")->fetch(\PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            return false;
        }

        $arr = explode("\n", $res[1]);
        $count = count($arr);
        $last_row = preg_replace("/(AUTO_INCREMENT=\d+\s)/", "", $arr[$count - 1]);
        $arr[$count - 1] = $last_row;

        if ($asArray) {
            return $arr;
        }
        return implode("\n", $arr);
    }
}