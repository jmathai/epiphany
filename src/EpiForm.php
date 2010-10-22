<?php
class EpiForm
{
  const __FIELD__ = '__EpiForm__';
  private $id;
  private $fields = array();
  private $slot   = 0;
  private $currentField = -1;
  private $debug = false;
  private $passField = null;
  private $failField = null;
  private $passForm = null;
  private $failForm = null;
  private $formParams = null;
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

  public function addType($type, $params = null)
  {
    call_user_func(array(__CLASS__, '_'.$type), $params);
    return $this;
  }

  public function getClientValidationJS()
  {
    $defs = array();
    $defs['defs'] = array();
    $defs['params'] = $this->formParams;
    foreach($this->fields as $i => $field)
    {
      $defs['defs'][$i] = array('el' => $field['id'], 'type' => $field['type']['rule'], 'args' => $field['type']['args'], 'listen' => $field['events'], 'msg' => $field['msg']);
    }

    $retval = $this->_jsInit() . ' Event.add(window, "load", function(){
      YAHOO.formValidator.initClientValidation(' . json_encode($defs) . ');';

    if($this->passField)
    {
      $retval .= 'YAHOO.formValidator.passFieldFunc = ' . trim($this->passField) . ";\n";
    }
    if($this->passForm)
    {
      $retval .= 'YAHOO.formValidator.passFormFunc = ' . trim($this->passForm) . ";\n";
    }
    if($this->failField)
    {
      $retval .= 'YAHOO.formValidator.failFieldFunc = ' . trim($this->failField) . ";\n";
    }
    if($this->failForm)
    {
      $retval .= 'YAHOO.formValidator.failFormFunc = ' . trim($this->failForm) . ";\n";
    }

    $retval .= '} );'; // close Event.add

    return $retval;
  }

  public function getFieldForServer()
  {
    return '<input type="hidden" name="' . self::__FIELD__ . '" value=\'' . json_encode($this->fields) . '\' />';
  }

  public function getRepopulateJS($str)
  {
    $fields = EpiFormServer::getDecodedString($str);
    return $this->_jsInit() . 'Event.add(window, "load", function(){ YAHOO.formValidator.repopulate(' . $fields . '); } );';
  }

  public function setDebug($bool)
  {
    $this->debug = (boolean)$bool;
    return $this;
  }

  public function setFormParams($params)
  {
    $this->formParams = $params;
  }

  public function setPassFieldFunction($js)
  {
    $this->passField = $js;
  }

  public function setPassFormFunction($js)
  {
    $this->passForm = $js;
  }

  public function setFailFieldFunction($js)
  {
    $this->failField = $js;
  }

  public function setFailFormFunction($js)
  {
    $this->failForm = $js;
  }


  private function _jsInit()
  {
    $retval = '';
    if($this->jsInit == null)
    {
      $args['form'] = $this->id;
      $args['debug']= $this->debug;
      $retval = '
        Event.add(window, "load", function(){
            YAHOO.formValidator.init(' . json_encode($args) . ');
          }
        );';
    }

    return $retval;
  }

  private function _email($args)
  {
    $this->fields[$this->currentField]['type'] = array('rule' => 'email', 'args' => (string)$args);
  }

  private function _maxChars($args)
  {
    $this->fields[$this->currentField]['type'] = array('rule' => 'maxChars', 'args' => (string)$args);
  }

  private function _required($args)
  {
    $this->fields[$this->currentField]['type'] = array('rule' => 'required', 'args' => (string)$args);
  }

  private function _sameAs($args)
  {
    $this->fields[$this->currentField]['type'] = array('rule' => 'sameAs', 'args' => (string)$args);
  }
  
  private function _zip($args)
  {
    $this->fields[$this->currentField]['type'] = array('rule' => 'zip', 'args' => (string)$args);
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

      if(!self::$type($_REQUEST[$name], $args))
      {
        $retval += $slot;
      }
    }

    return $retval;
  }

  public function getFieldsByError($errorCode = 0)
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

  private static function _email($val, $args)
  {
    return stristr($val, '@') !== false; //preg_match('/^[A-z0-9_\-]+\@(A-z0-9_-]+\.)+[A-z]{2,4}$/', $val) === false;
  }

  private static function _maxChars($val, $args)
  {
    return mb_strlen($val) <= $args; // false if longer than $args
  }

  private static function _required($val)
  {
    return !empty($val); // false if $args is blank
  }

  private static function _sameAs($val, $args)
  {
    return $val == $_REQUEST[$args]; // false if not same as $args
  }
  
  private static function _zip($val, $args)
  {
    return preg_match('/\d{5}/', $val); // false if not 5 ints
  }
}
/*
 * Modification History:
 *   06/19/2008 - JM
 *    - Ported from prototype.js to YUI and added server-side validation.
 *  01/15/2007 - JM
 *    - Added mod10 function
 *  03/08/2002 - JM
 *    - Fixed bug associated with using in_array and replaced certain in_array w/ array_search
 *  02/26/2002 - JM
 *    - Converted from cfml to php
 *  07/26/2001 - JM
 *    - Added streamline feature so only needed validation is sent to browser.
 *  06/30/2001 - JM
 *    - Added variable name for function for more than one use on a page.
 *  03/15/2001 - JM
 *    - Added option to output debugging information.  Useful when the tag is throwing javascript errors.
 *    - Added maxElementsToDisplay to keep alert box from becoming too large (vertically).
 *  03/06/2001 - JM
 *    - Added ability to validate for minimum and maximum checkboxes
 *    - Added ability to validate using regular expressions
 *  03/05/2001 - JM
 *    - Added minimumlength, maximumlength, and exact length
 *    - Modified theseFields to replace single quotes with "\'"
 *  02/23/2001 - JM
 *    - Added ability to validate against selection lists
 *  02/20/2001 - JM
 *    - Added ability to accept field names in addition to field numbers
 *    - Modified javascript variable to be assigned form object instead of name/number of form
 *  02/16/2001 - JM
 *    - Added minimumdate and maximumdate validation types
 *  02/15/2001 - JM
 *    - Added minimumnumber and maximumnumber validation types
*/
?>
