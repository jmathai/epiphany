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

  public static function addForm($id)
  {
    return new EpiForm($id);
  }

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

  public function debug($bool)
  {
    $this->debug = (boolean)$bool;
    return $this;
  }

  public function overloadPass($js)
  {
    $this->pass = $js;
  }

  public function overloadFail($js)
  {
    $this->fail = $js;
  }

  public function prepareForServer()
  {
    return '<input type="hidden" name="' . self::__FIELD__ . '" value=\'' . json_encode($this->fields) . '\' />';
  }

  public function repopulate($str)
  {
    $fields = EpiFormServer::decode($str);
    return 'YAHOO.formValidator.repopulate(' . $fields . ');';
  }

  public function validateJS()
  {
    // YAHOO.formValidator.init({"form":"f","defs":[{"el":"i","type":"maxChars","params":5,"event":["keyup","mouseup"]}]});
    $retval = array();
    $retval['form'] = $this->id;
    $retval['debug']= $this->debug;
    $retval['defs'] = array();
    foreach($this->fields as $i => $field)
    {
      $retval['defs'][$i] = array('el' => $field['id'], 'type' => $field['type']['rule'], 'args' => $field['type']['args'], 'event' => $field['events'], 'msg' => $field['msg']);
    }

    $retval = 'YAHOO.formValidator.init(' . json_encode($retval) . ');';
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

  private function _maxChars($args)
  {
    $this->fields[$this->currentField]['type'] = array('rule' => 'maxChars', 'args' => (string)$args);
  }
}

class EpiFormServer
{
  public static $definitions;

  public static function checkFields()
  {
    $retval = 0;
    if(empty(self::$definitions))
    {
      self::$definitions = self::generateDefinitions();
    }

    foreach(self::$definitions as $def)
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

  public static function encode($_post = null)
  {
    if($_post === null)
    {
      $_post = $_POST;
    }
    unset($_post[EpiForm::__FIELD__]);

    return base64_encode(json_encode($_post));
  }

  public static function decode($str)
  {
    return base64_decode($str);
  }

  private static function generateDefinitions()
  {
    if(empty(self::$definitions))
    {
      self::$definitions = json_decode($_REQUEST[EpiForm::__FIELD__], 1);
    }

    return (array) self::$definitions;
  }

  private static function _maxChars($val, $args)
  {
    return mb_strlen($val) > $args; // false if longer than $args
  }
}
?>
