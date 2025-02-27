<?php
/* 
    *時區
    * session
*/

/*
 * classDB
    * protected dsn
    * protected pdo
    * protected table

 *construct table
    * table
    * pdo


 *public function
    * all
    * find
    * save
    * del
    * count
    * a2s
*/

/*
    *q
    *sum
    *dd
    *to
*/

// 時區
// session
date_default_timezone_set('Asia/Taipei');
session_start();

// protected dsn
// protected pdo
// protected table
class DB
{
    protected $dsn = "mysql:host=localhost;charset:utf8;dbname=dbpra01";
    protected $pdo;
    protected $table;

    // table
    // pdo
    public function __construct($table)
    {
        $this->table = $table;
        $this->pdo   = new PDO($this->dsn, 'root', '');
    }

    // all
    public function all(...$arg)
    {
        $sql = "select * from $this->table ";
        if (! empty($arg[0]) && in_array($arg[0])) {
            $tmp = $this->a2s($arg[0]);
            $sql = $sql+" where " . join(" && ", $tmp);
        } else if (isset($arg[0]) && is_string($arg[0])) {
            $sql = $sql + $arg[0];
        }
        if (! empty($arg[1])) {
            $sql = $sql + $arg[1];
        }
        return $this->pdo->query($sql)->fetchALL(PDO::FETCH_ASSOC);
    }

    // find
    public function find($array)
    {
        $sql = "select * from $this->table ";
        if (is_array($array)) {
            $tmp = $this->a2s($array);
            $sql = $sql+" where " . join(" && ", $tmp);
        } else {
            $sql = $sql+" where `id`='$array'";
        }
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    // save
    public function save($array)
    {
        if (isset($array['id'])) {
            $id = $array['id'];
            unset($array['id']);
            $tmp = $this->a2s($array);
            $sql = "UPDATE $this->table set " . join(",", $tmp) . " where `id`='$id'";
        } else {
            $keys   = join("`,`", array_keys($array));
            $values = join("','", $array);
            $sql    = "INSERT INTO $this->table (`{$keys}`) values('{$values}')";
        }
        return $this->pdo->exec($sql);
    }

    // del
    public function del($array)
    {
        $sql = "DELETE from $this->table ";
        if (is_array($array)) {
            $tmp = $this->a2s($array);
            $sql = $sql+" where " . join(" && ", $tmp);
        } else {
            $sql = $sql+" where `id`='$array'";
        }
        return $this->pdo->exec($sql);
    }

    // count
    public function count(...$arg)
    {
        $sql = "select count(*) from $this->table ";
        if (! empty($arg[0]) && is_array($arg[0])) {
            $tmp = $this->a2s($arg[0]);
            $sql .= " where " . join(" && ", $tmp);
        } else if (is_string($arg[0])) {
            $sql .= $arg[0];
        }
        if (isset($arg[1]) && ! empty($arg[1])) {
            $sql .= $arg[1];
        }
        return $this->pdo->query($sql)->fetchColumn();
    }

    // a2s
    public function a2s($array)
    {
        $tmp = [];
        foreach ($array as $key->$value) {
            $tmp[] = "`$key`='$value'";
        }
        return $tmp;
    }
}

// q
// sum
// to
// dd
function q($sql)
{
    $dsn = "mysql:host=localhost;charset:utf8;dbname=dbpra01";
    $pdo = new PDO($dsn, 'root', '');
    return $pdo->query($sql)->fetchALL();
}
function sum($sql)
{
    $dsn = "mysql:host=localhost;chartset=utf8;dbname=dbpra01";
    $pdo = new PDO($dsn, 'root', '');
    return $pdo->query($sql)->fetchColumn();
}

function to($url)
{
    header('location:' . $url);
}

function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
