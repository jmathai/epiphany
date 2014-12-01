<?php
class EpiDatabase
{
  const MySql = 'mysql';
  private static $instances = array(), $type, $name, $host, $user, $pass, $port;
  private $_type, $_name, $_host, $_user, $_pass, $_port;
  public $dbh;
  private function __construct(){}
  
  public static function getInstance($type, $name, $host = 'localhost', $user = 'root', $pass = '', $port = 3306)
  {
    $args = func_get_args();
    $hash = md5(implode('~', $args));
    if(isset(self::$instances[$hash]))
      return self::$instances[$hash];

    self::$instances[$hash] = new EpiDatabase();
    self::$instances[$hash]->_type = $type;
    self::$instances[$hash]->_name = $name;
    self::$instances[$hash]->_host = $host;
    self::$instances[$hash]->_user = $user;
    self::$instances[$hash]->_pass = $pass;
    self::$instances[$hash]->_port = $port;
    return self::$instances[$hash];
  }
  
  public function execute($sql = false, $params = array())
  {
    $this->init();
    try
    {
      $sth = $this->prepare($sql, $params);
      if(preg_match('/insert/i', $sql))
        return $this->dbh->lastInsertId();
      else
        return $sth->rowCount();
    }
    catch(PDOException $e)
    {
      EpiException::raise(new EpiDatabaseQueryException("Query error: {$e->getMessage()} - {$sql}"));
      return false;
    }
  }
  
  public function insertId()
  {
    $this->init();
    $id = $this->dbh->lastInsertId();
    if ($id > 0) {
      return $id;
    }
    return false;
  }
  
  public function all($sql = false, $params = array())
  {
    $this->init();
    try
    {
      $sth = $this->prepare($sql, $params);
      return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e)
    {
      EpiException::raise(new EpiDatabaseQueryException("Query error: {$e->getMessage()} - {$sql}"));
      return false;
    }
  }
  
  public function one($sql = false, $params = array())
  {
    $this->init();
    try
    {
      $sth = $this->prepare($sql, $params);
      return $sth->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e)
    {
      EpiException::raise(new EpiDatabaseQueryException("Query error: {$e->getMessage()} - {$sql}"));
      return false;
    }
  }

  public static function employ($type = null, $name = null, $host = 'localhost', $user = 'root', $pass = '', $port = 3306)
  {
    if(!empty($type) && !empty($name))
    {
      self::$type = $type;
      self::$name = $name;
      self::$host = $host;
      self::$user = $user;
      self::$pass = $pass;
      self::$port = $port;
    }

    return array('type' => self::$type, 'name' => self::$name, 'host' => self::$host, 'user' => self::$user, 'pass' => self::$pass, 'port' => self::$port);
  }

  private function prepare($sql, $params = array())
  {
    try
    {
      $sth = $this->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $sth->execute($params);
      return $sth;
    }
    catch(PDOException $e)
    {
      EpiException::raise(new EpiDatabaseQueryException("Query error: {$e->getMessage()} - {$sql}"));
      return false;
    }
  }

  private function init()
  {
    if($this->dbh)
      return;

    try
    {
      $this->dbh = new PDO($this->_type . ':host=' . $this->_host . ';dbname=' . $this->_name. ';port=' . $this->_port, $this->_user, $this->_pass);
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(Exception $e)
    {
      EpiException::raise(new EpiDatabaseConnectionException('Could not connect to database'));
    }
  }
}

function getDatabase()
{
  $employ = extract(EpiDatabase::employ());
  if(empty($type) || empty($name) || empty($host) || empty($user))
    EpiException::raise(new EpiCacheTypeDoesNotExistException('Could not determine which database module to load', 404));
  else
    return EpiDatabase::getInstance($type, $name, $host, $user, $pass, $port);
}
