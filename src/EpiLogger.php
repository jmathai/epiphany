<?php
class EpiLogger
{
  const Info = 'info';
  const Warn = 'warn';
  const Crit = 'crit';

  public function __construct() { }
  
  public function crit($message, $exception = null)
  {
    $this->log($message, self::Crit, $exception);
  }
  
  public function info($message, $exception = null)
  {
    $this->log($message, self::Info, $exception);
  }
  
  public function warn($message, $exception = null)
  {
    $this->log($message, self::Warn, $exception);
  }

  private function parseException($exception)
  {
    return "{file:{$exception->getFile()}, line:{$exception->getLine()}, message:\"{$exception->getMessage()}\", trace:\"{$exception->getTraceAsString()}\"}";
  }

  private function log($description, $severity, $exception=null)
  {
    if($exception instanceof Exception)
      $additional = $this->parseException($exception);
    else
      $additional = '';

    error_log("{severity:{$severity}, description:\"{$description}\", additional:{$additional}}");
  }
}

function getLogger()
{
  static $logger;
  if($logger)
    return $logger;

  $logger = new EpiLogger();
  return $logger;
}
