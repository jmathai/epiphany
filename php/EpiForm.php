<?php
class EpiForm
{
  const __FIELD__ = '__EpiForm__';
  private $id;
  private $fields = array();
  private $slot   = 0;
  private $currentField = -1;
  private $debug = false;
  private $pass = null;
  private $fail = null;
  private $jsInit = null;

  public function __construct($id)
  {
    $this->id = $id;
  }

  public function addEvent($event)
  {
    $this->fields[$this->currentField]['events'][] = $event;
    return $this;
  }

  public function addField($idOfField)
  {
    $slot = pow(2, $this->slot++);
    $this->fields[++$this->currentField] = array('id' => $idOfField, 'slot' => $slot);
    return $this;
  }

  public function addMessage($msg)
  {
    $this->fields[$this->currentField]['msg'] = $msg;
  }

  public function addType($type, $params)
  {
    call_user_func(array(__CLASS__, '_'.$type), $params);
    return $this;
  }

  public function setDebug($bool)
  {
    $this->debug = (boolean)$bool;
    return $this;
  }

  public function getClientValidationJS()
  {
    $retval = array();
    $retval['defs'] = array();
    foreach($this->fields as $i => $field)
    {
      $retval['defs'][$i] = array('el' => $field['id'], 'type' => $field['type']['rule'], 'args' => $field['type']['args'], 'event' => $field['events'], 'msg' => $field['msg']);
    }

    $retval = $this->_jsInit() . 'YAHOO.formValidator.initClientValidation(' . json_encode($retval) . ');';
    if($this->pass)
    {
      $retval .= 'YAHOO.formValidator.pass = ' . trim($this->pass) . ';';
    }
    if($this->fail)
    {
      $retval .= 'YAHOO.formValidator.fail = ' . trim($this->fail) . ';';
    }

    return $retval;
  }

  public function getFieldForServer()
  {
    return '<input type="hidden" name="' . self::__FIELD__ . '" value=\'' . json_encode($this->fields) . '\' />';
  }

  public function getRepopulateJS($str)
  {
    $fields = EpiFormServer::getDecodedString($str);
    return $this->_jsInit() . 'YAHOO.formValidator.repopulate(' . $fields . ');';
  }

  public function setPassFunction($js)
  {
    $this->pass = $js;
  }

  public function setFailFunction($js)
  {
    $this->fail = $js;
  }

  private function _jsInit()
  {
    $retval = '';
    if($this->jsInit == null)
    {
      $args['form'] = $this->id;
      $args['debug']= $this->debug;
      $retval = 'YAHOO.formValidator.init(' . json_encode($args) . ');';
    }

    return $retval;
  }

  private function _maxChars($args)
  {
    $this->fields[$this->currentField]['type'] = array('rule' => 'maxChars', 'args' => (string)$args);
  }
}

class EpiFormServer
{
  private $definitions;

  public function __construct()
  {
    $this->definitions = $this->getDefinitions();
  }

  public function getResult()
  {
    $retval = 0;
    foreach($this->definitions as $def)
    {
      $slot = $def['slot'];
      $name = $def['id'];
      $type = '_' . $def['type']['rule'];
      $args = $def['type']['args'];

      if(self::$type($_REQUEST[$name], $args))
      {
        $retval += $slot;
      }

    }

    return $retval;
  }

  public function getFieldsByError($result = 0)
  {
    $retval = array();
    foreach($this->definitions as $field)
    {
      if(($field['slot'] & $errorCode) == $field['slot'])
      {
        $retval[] = $field['id'];
      }
    }

    return $retval;
  }

  public function getEncodedString($_post = null)
  {
    if($_post === null)
    {
      $_post = $_POST;
    }
    unset($_post[EpiForm::__FIELD__]);

    return base64_encode(json_encode($_post));
  }

  public function getDecodedString($str)
  {
    return base64_decode($str);
  }

  private function getDefinitions()
  {
    $this->definitions = json_decode($_REQUEST[EpiForm::__FIELD__], 1);
    return (array) $this->definitions;
  }

  private static function _maxChars($val, $args)
  {
    return mb_strlen($val) > $args; // false if longer than $args
  }
}
?>
