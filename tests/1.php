<?php

require_once __DIR__ . '/../CompareDatabases.php';

use Moon\Tools\CompareDatabases;

$dbms = 'mysql';        //数据库类型
$host = 'localhost';    //数据库主机名
$user = 'test';         //数据库连接用户名
$password = '';         //对应的密码

$dbName1 = 'test1';     //使用的数据库1
$dbName2 = 'test2';     //使用的数据库2

if (file_exists(__DIR__ . '/env.php')) {
    require_once __DIR__ . '/env.php';
}

$db1 = new PDO("$dbms:host=$host;dbname=$dbName1;charset=utf8mb4", $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db2 = new PDO("$dbms:host=$host;dbname=$dbName2;charset=utf8mb4", $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$comparer = new CompareDatabases($db1, $db2);
$res = $comparer->compare();

$data = '';
foreach ($res as $table => $diff) {
    $data .= "-- ----------------------- `$table` -----------------------\n";
    $data .= "-- ------------ table1:\n" . $diff[0] . "\n";
    $data .= $diff[1] ? "-- ------------ table2:\n" . $diff[1] . "\n\n" : "-- table not exists!\n\n";
}
file_put_contents(__DIR__ . '/res.sql', $data);
