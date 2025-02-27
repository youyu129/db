<?php
date_default_timezone_set('Asia/Taipei');
session_start();

class DB
{
    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=db13";
    protected $pdo;
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
        $this->pdo   = new PDO($this->dsn, 'root', '');
    }

    public function all(...$arg)
    {
        $sql = "select * from $this->table ";
        if (! empty($arg[0]) && is_array($arg[0])) {
            $tmp = $this->a2s($arg[0]);
            $sql .= " where " . join(" && ", $tmp);
        } else if (isset($arg[0]) && is_string($arg[0])) {
            $sql .= $arg[0];
        }
        if (! empty($arg[1])) {
            $sql .= $arg[1];
        }
        return $this->pdo->query($sql)->fetchALL(PDO::FETCH_ASSOC);
    }

    public function find($array)
    {
        $sql = "select * from $this->table ";
        if (is_array($array)) {
            $tmp = $this->a2s($array);
            $sql .= " where " . join(" && ", $tmp);
        } else {
            $sql .= " where `id`='$array'";
        }
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

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

    public function del($array)
    {
        $sql = "DELETE from $this->table ";
        if (is_array($array)) {
            $tmp = $this->a2s($array);
            $sql .= " where " . join(" && ", $tmp);
        } else {
            $sql .= " where `id`='$array'";
        }
        return $this->pdo->exec($sql);
    }

    public function count(...$arg)
    {
        $sql = "SELECT count(*) FROM $this->table ";
        if (! empty($arg[0])) {
            if (is_array($arg[0])) {
                $where = $this->a2s($arg[0]);
                $sql   = $sql . " WHERE " . join(" AND ", $where);
            } else {
                $sql .= $arg[0];
            }
        }
        if (! empty($arg[1])) {
            $sql = $sql . $arg[1];
        }
        return $this->pdo->query($sql)->fetchColumn();
    }

    public function a2s($array)
    {
        $tmp = [];
        foreach ($array as $key => $value) {
            $tmp[] = "`$key`='$value'";
        }
        return $tmp;
    }
}

function q($sql)
{
    $dsn = "mysql:host=localhost;charset=utf8;dbname=db13";
    $pdo = new PDO($dsn, 'root', '');
    return $pdo->query($sql)->fetchALL();
}
function sum($sql)
{
    $dsn = "mysql:host=localhost;chartset=utf8;dbname=db13";
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

$Total = new DB('total');
$User  = new DB('users');
$News  = new DB('news');
$Que   = new DB('que');
$Log   = new DB('log');

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
