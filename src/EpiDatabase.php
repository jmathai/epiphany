<?php
class EpiDatabase
{
  const MySql = 'mysql';
  private static $instances = array(), $type, $name, $host, $user, $pass;
  public $dbh;
  private function __construct(){}
  
  public static function getInstance($type, $name, $host = 'localhost', $user = 'root', $pass = '')
  {
    $args = func_get_args();
    $hash = md5(implode('~', $args));
    if(self::$instances[$hash])
      return self::$instances[$hash];

    self::$instances[$hash] = new EpiDatabase();
    self::$instances[$hash]->type = $type;
    self::$instances[$hash]->name = $name;
    self::$instances[$hash]->host = $host;
    self::$instances[$hash]->user = $user;
    self::$instances[$hash]->pass = $pass;
    return self::$instances[$hash];
  }
  
  public function queryAll($sql)
  {
    $this->init();
    $retval = array();
    try
    {
      $rs = $this->dbh->query($sql, PDO::FETCH_ASSOC);
      if($rs)
      {
        foreach($rs as $row)
        {
          $retval[] = $row;
        }
      }
      
      return $retval;
    }
    catch(PDOException $e)
    {
      EpiException::raise(new EpiDatabaseQueryException("Query error: {$e->getMessage()} - {$sql}"));
    }
  }
  
  public function queryFirst($sql)
  {
    $this->init();
    try
    {
      $rs = $this->dbh->query($sql);
      $retval = $rs->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e)
    {
      EpiException::raise(new EpiDatabaseQueryException("Query error: {$e->getMessage()} - {$sql}"));
    }

    return $retval;
  }
  
  public function escape($string, $nulls = false)
  {
    if(is_array($string))
    {
      return $this->escapeArray($string);
    }
    elseif($string !== '' && !$nulls)
    {
      return '\'' . addslashes($string) .  '\'';
    }
    else
    {
      return 'NULL';
    }
  }
  
  public function escapeArray($strings)
  {
    foreach($strings as $k => $v)
    {
      $strings[$k] = $this->sqlSafe($v);
    }

    return $strings;
  }
  
  public function insertId()
  {
    $this->init();
    $id = $this->dbh->lastInsertId();
    if ( $id > 0 ) {
      return $id;
    }
    return false;
  }
  
  public function execute( $sql = false )
  {
    $this->init();
    try
    {
      $retval = $this->dbh->exec($sql);
    }
    catch(PDOException $e)
    {
      EpiException::raise(new EpiDatabaseQueryException("Query error: {$e->getMessage()} - {$sql}"));
    }

    return $retval;
  }
  
  public function query( $sql = false )
  {
    $this->init();
    
    $retval = null;
    try
    {
      $retval = $this->dbh->query($sql, PDO::FETCH_ASSOC);
      return $retval;
    }
    catch(PDOException $e)
    {
      EpiException::raise(new EpiDatabaseQueryException("Query error: {$e->getMessage()} - {$sql}"));
    }
  }
  
  public function numRows( $result = false ) {
    if ( $result ) {
      $rows = mysql_num_rows( $result );
      if ( $rows > 0 ) {
        return $rows;
      }
    }
    return false;
  }
  
  public function foundRows()
  {
    $ar = $this->queryFirst('SELECT FOUND_ROWS() AS _FOUND_ROWS');
    return $ar['_FOUND_ROWS'];
  }

  public static function employ($type = null, $name = null, $host = 'localhost', $user = 'root', $pass = '')
  {
    if(!empty($type) && !empty($name))
    {
      self::$type = $type;
      self::$name = $name;
      self::$host = $host;
      self::$user = $user;
      self::$pass = $pass;
    }

    return array('type' => self::$type, 'name' => self::$name, 'host' => self::$host, 'user' => self::$user, 'pass' => self::$pass);
  }

  private function init()
  {
    if($this->dbh)
      return;

    try
    {
      $this->dbh = PDO($this->type . ':host=' . $this->host . ';dbname=' . $this->name, $this->user, $this->pass);
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(Exception $e)
    {
      EpiException::raise(new EpiDatabaseConnectionException('Could not connect to database'));
    }
  }
}

function getDb()
{
  $employ = extract(EpiDatabase::employ());
  if(empty($type) || empty($name) || empty($host) || empty($user))
    EpiException::raise(new EpiCacheTypeDoesNotExistException('Could not determine which database module to load', 404));
  else
    return EpiDatabase::getInstance($type, $name, $host, $user, $pass);
}
