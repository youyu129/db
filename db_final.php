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
date_default_timezone_set("Asia/Taipei");
session_start();

// protected dsn
// protected pdo
// protected table
class DB
{
    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=db13";
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
        $sql = "SELECT * FROM $this->table ";
        if (! empty($arg[0]) && is_array($arg[0])) {
            $tmp = $this->a2s($arg[0]);
            $sql .= " WHERE " . join(" && ", $tmp);
        } else if (isset($arg[0]) && is_string($arg[0])) {
            $sql .= $arg[0];
        }
        if (! empty($arg[1])) {
            $sql .= $arg[1];
        }
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // find
    public function find($array)
    {
        $sql = "SELECT * FROM $this->table ";
        if (is_array($array)) {
            $tmp = $this->a2s($array);
            $sql .= " WHERE " . join(" && ", $tmp);
        } else {
            $sql .= " WHERE `id`='$array'";
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
            $sql = "UPDATE $this->table SET " . join(",", $tmp) . " WHERE `id`='$id'";
        } else {
            $keys   = join("`,`", array_keys($array));
            $values = join("','", $array);
            $sql    = "INSERT INTO $this->table (`{$keys}`) VALUES('{$values}')";
        }
        return $this->pdo->exec($sql);
    }

    // del
    public function del($array)
    {
        $sql = "DELETE FROM $this->table ";
        if (is_array($array)) {
            $tmp = $this->a2s($array);
            $sql .= " WHERE " . join(" && ", $tmp);
        } else {
            $sql .= " WHERE `id`='$array'";
        }
        return $this->pdo->exec($sql);
    }

    // count
    public function count(...$arg)
    {
        $sql = "SELECT count(*) FROM $this->table ";
        if (! empty($arg[0])) {
            if (is_array($arg[0])) {
                $tmp = $this->a2s($arg[0]);
                $sql .= " WHERE " . join(" && ", $tmp);
            } else {
                $sql .= $arg[0];
            }
        }
        if (! empty($arg[1])) {
            $sql .= $arg[1];
        }
        return $this->pdo->query($sql)->fetchColumn();
    }

    // a2s
    public function a2s($array)
    {
        $tmp = [];
        foreach ($array as $key => $value) {
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
    $dsn = "mysql:host=localhost;charset=utf8;dbname=db13";
    $pdo = new PDO($dsn, 'root', '');
    return $pdo->query($sql)->fetchAll();
}
function sum($sql)
{
    $dsn = "mysql:host=localhost;charset=utf8;dbname=db13";
    $pdo = new PDO($dsn, 'root', '');
    return $pdo->query($sql)->fetchColumn();
}

function to($url)
{
    header("location:" . $url);
}

function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

$Total = new DB('total');
$User  = new DB('users');
$News  = new DB('news');
$Que   = new DB('que');
$Log   = new DB('log');

// 如果沒來過的人
if (! isset($_SESSION['view'])) {
    if ($Total->count(['day' => date("Y-m-d")]) > 0) {
        $total = $Total->find(['day' => date("Y-m-d")]);
        $total['total']++;
        $Total->save($total);
    } else {
        $Total->save(['day' => date("Y-m-d"), 'total' => 1]);
    }
    $_SESSION['view'] = 1;
}
