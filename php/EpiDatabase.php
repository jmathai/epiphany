<?php
class EpiDatabase
{
  static protected $dbh;
  
  public static function connect($type=null, $host=null, $name=null, $user=null,
    $pass=null)
  {
    if(!self::$dbh)
    {
      if($type === null && defined('DB_TYPE')){ $type = DB_TYPE; }
      if($host === null && defined('DB_HOST')){ $host = DB_HOST; }
      if($name === null && defined('DB_NAME')){ $name = DB_NAME; }
      if($user === null && defined('DB_USER')){ $user = DB_USER; }
      if($pass === null && defined('DB_PASS')){ $pass = DB_PASS; }
      
      try
      {
        self::$dbh = new PDO($type . ':host=' . $host . ';dbname=' . $name, $user, $pass);
        self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$dbh;
      }
      catch(Exception $e)
      {
        throw new EpiDatabaseConnectionException('Could not connect to database', EpiException::EPI_EXCEPTION_DB_CONNECTION);
      }
    }
    else 
    {
      return self::$dbh;
    }
  }
  
  public static function query_all($sql)
  {
    self::check();
    
    $retval = array();
    try
    {
      $rs = self::$dbh->query($sql, PDO::FETCH_ASSOC);
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
      $message = $e->getMessage();
      throw new EpiDatabaseQueryException("Query error: {$message} - {$sql}", EpiException::EPI_EXCEPTION_DB_QUERY);
    }
  }
  
  public static function query_first($sql)
  {
    self::check();
    
    try
    {
      $rs = self::$dbh->query($sql);
      $retval = $rs->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e)
    {
      $message = $e->getMessage();
      throw new EpiDatabaseQueryException("Query error: {$message} - {$sql}", EpiException::EPI_EXCEPTION_DB_QUERY);
    }

    return $retval;
  }
  
  public static function sql_safe($string)
  {
    if(strlen($string) > 0) {
      return '\'' . addslashes($string) .  '\'';
    }else {
      return 'NULL';
    }
  }
  
  public static function asql_safe($var_array)
  {
    $temp_array = array();
    
    foreach($var_array as $k => $v)
    {
      $temp_array[$k] = self::sql_safe($v, $allow_nulls);
    }

    return $temp_array;
  }
  
  public static function insert_id()
  {
    self::check();
    $id = self::$dbh->lastInsertId();
    if ( $id > 0 ) {
      return $id;
    }
    return false;
  }
  
  public static function execute( $sql = false )
  {
    self::check();
    try
    {
      $retval = self::$dbh->exec($sql);
    }
    catch(PDOException $e)
    {
      $message = $e->getMessage();
      throw new EpiDatabaseQueryException("Query error: {$message} - {$sql}", EpiException::EPI_EXCEPTION_DB_QUERY);
    }
  }
  
  public static function query( $sql = false )
  {
    self::check();
    
    $retval = null;
    try
    {
      $retval = self::$dbh->query($sql, PDO::FETCH_ASSOC);
      return $retval;
    }
    catch(PDOException $e)
    {
      $message = $e->getMessage();
      throw new EpiDatabaseQueryException("Query error: {$message} - {$sql}", EpiException::EPI_EXCEPTION_DB_QUERY);
    }
  }
  
  public static function num_rows( $result = false ) {
    if ( $result ) {
      $rows = mysql_num_rows( $result );
      if ( $rows > 0 ) {
        return $rows;
      }
    }
    return false;
  }
  
  public static function found_rows()
  {
    $ar = self::query_first('SELECT FOUND_ROWS() AS _FOUND_ROWS');
    return $ar['_FOUND_ROWS'];
  }

  private static function check()
  {
    if(!self::$dbh)
    {
      self::connect();
    }
  }
}

if(!class_exists('EpiException')){
  class EpiException extends Exception{}
}

class EpiDatabaseException extends EpiException{}
class EpiDatabaseConnectionException extends EpiDatabaseException{}
class EpiDatabaseQueryException extends EpiDatabaseException{}
?>
