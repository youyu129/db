<?php

date_default_timezone_set("Asia/Taipei");
session_start();

class DB
{
    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=db18";
    protected $pdo;
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
        $this->pdo   = new PDO($this->dsn, 'root', '');
    }

// 裡面的function
// all
// find
// save
// del
// count
// math
// arrayToSql
// fetch_one
// fetch_all

// all
// table後面記得要加空白
// 如果第一個參數有資料 而且他是陣列 if 得到這個陣列 當成where條件裡面的資料
// 如果第一個參數不是空的 但他不是陣列(他是字串) else 把他串在一起
// 如果第一個參數是空的 不管他(不用寫)
// 如果第二個參數有資料 就當成字串接在後面
// 撈出全部資料
    public function all(...$arg)
    {
        $sql = "select * from $this->table ";
        if (! empty($arg[0]) && is_array($arg[0])) {
            $tmp = $this->arrayToSql($arg[0]);
            $sql .= " where " . join(" && ", $tmp);
        } else if (isset($arg[0]) && is_string($arg[0])) {
            $sql .= $arg[0];
        }

        if (! empty($arg[1])) {
            $sql .= $arg[1];
        }
        return $this->fetch_all($sql);
    }

// find
// 判斷是數字id 或 陣列
// 如果他是陣列 把他拚起來
// 如果他不是陣列 他給的是id值(我們自己定義的) 就寫sql定義id
// find只要一筆資料
    public function find($array)
    {
        $sql = "select * from $this->table ";
        if (is_array($array)) {
            $tmp = $this->arrayToSql($array);
            $sql .= " where " . join(" && ", $tmp);
        } else {
            $sql .= " where `id`='$array'";
        }
        return $this->fetch_one($sql);
    }

// save
// 如果有id就更新update
// 其他就更新
// 把陣列的key值包起來變成一個陣列
// 用`,``,``,`把欄位名稱串起來
// 用','把欄位的內容串起來
// 把key跟value放進去
// 如果成功會回傳1以上 如果失敗會傳0
    public function save($array)
    {
        if (isset($array['id'])) {
            $id = $array['id'];
            unset($array['id']);
            $tmp = $this->arrayToSql($array);
            $sql = "update $this->table set " . join(",", $tmp) . " where `id`='$id'";
        } else {
            $keys   = join("`,`", array_keys($array));
            $values = join("','", $array);
            $sql    = "insert into $this->table (`{$keys}`) values('{$values}')";
        }
        return $this->pdo->exec($sql);
    }

// del
// 從這個資料表刪除
    public function del($array)
    {
        $sql = "delete from $this->table ";
        if (is_array($array)) {
            $tmp = $this->arrayToSql($array);
            $sql .= " where " . join(" && ", $tmp);
        } else {
            $sql .= " where `id`='$array'";
        }
        return $this->pdo->exec($sql);
    }

// count
// 計算的動作
// 回傳他的值
    public function count(...$arg)
    {
        $sql = "select count(*) from $this->table ";
        if (! empty($arg[0]) && is_array($arg[0])) {
            $tmp = $this->arrayToSql($arg[0]);
            $sql .= " where " . join(" && ", $tmp);
        } else if (is_string($arg[0])) {
            $sql .= $arg[0];
        }

        if (isset($arg[0]) && ! empty($arg[1])) {
            $sql .= $arg[1];
        }
        return $this->pdo->query($sql)->fetchColumn();
    }

// math

// arrayToSql
// 先定義空陣列
// 取出key和value
// 組合成字串
    public function arrayToSql($array)
    {
        $tmp = [];
        foreach ($array as $key => $value) {
            $tmp[] = "`$key`='$value'";
        }
        return $tmp;
    }

// fetch_one
    public function fetch_one($sql)
    {
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }
// fetch_all
    public function fetch_all($sql)
    {
        return $this->pdo->query($sql)->fetchALL(PDO::FETCH_ASSOC);
    }

}

// 外面的function
// q
// to
// dd

// q
function q($sql)
{
    $dsn = "mysql:host=localhost;charset=utf8;dbname=db18";
    $pdo = new PDO($dsn, 'root', '');
}

// to
function to($url)
{
    header("location:" . $url);
}

// dd
function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

$Mem   = new DB('members');
$Admin = new DB('admins');
$Bot   = new DB('bottoms');
$Type  = new DB('types');
$Item  = new DB('items');
$Order = new DB('orders');
