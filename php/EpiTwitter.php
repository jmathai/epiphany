<?php
/*
 *  Class to integrate with Twitter's API.
 *    Authenticated calls are done using OAuth and require access tokens for a user.
 *    API calls which do not require authentication do not require tokens (i.e. search/trends)
 * 
 *  Full documentation available on github
 *    http://wiki.github.com/jmathai/epicode/epitwitter
 * 
 *  @author Jaisen Mathai <jaisen@jmathai.com>
 */
class EpiTwitter extends EpiOAuth
{
  const EPITWITTER_SIGNATURE_METHOD = 'HMAC-SHA1';
  protected $requestTokenUrl= 'http://twitter.com/oauth/request_token';
  protected $accessTokenUrl = 'http://twitter.com/oauth/access_token';
  protected $authorizeUrl   = 'http://twitter.com/oauth/authorize';
  protected $authenticateUrl= 'http://twitter.com/oauth/authenticate';
  protected $apiUrl         = 'http://twitter.com';
  protected $searchUrl      = 'http://search.twitter.com';

  public function __call($name, $params = null)
  {
    $parts  = explode('_', $name);
    $method = strtoupper(array_shift($parts));
    $parts  = implode('_', $parts);
    $path   = '/' . preg_replace('/[A-Z]|[0-9]+/e', "'/'.strtolower('\\0')", $parts) . '.json';
    if(!empty($params))
      $args = array_shift($params);

    // intercept calls to the search api
    if(preg_match('/^(search|trends)/', $parts))
    {
      $query = isset($args) ? http_build_query($args) : '';
      $url = "{$this->searchUrl}{$path}?{$query}";
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      return new EpiTwitterJson(EpiCurl::getInstance()->addCurl($ch));
    }

    return new EpiTwitterJson(call_user_func(array($this, 'httpRequest'), $method, "{$this->apiUrl}{$path}", $args));
  }

  public function __construct($consumerKey = null, $consumerSecret = null, $oauthToken = null, $oauthTokenSecret = null)
  {
    parent::__construct($consumerKey, $consumerSecret, self::EPITWITTER_SIGNATURE_METHOD);
    $this->setToken($oauthToken, $oauthTokenSecret);
  }
}

class EpiTwitterJson implements ArrayAccess, Countable,  IteratorAggregate
{
  private $__resp;
  public function __construct($response)
  {
    $this->__resp = $response;
  }

  // Implementation of the IteratorAggregate::getIterator() to support foreach ($this as $...)
  public function getIterator ()
  {
    return new ArrayIterator($this->__obj);
  }

  // Implementation of Countable::count() to support count($this)
  public function count ()
  {
    return count($this->__obj);
  }
  
  // Next four functions are to support ArrayAccess interface
  // 1
  public function offsetSet($offset, $value) 
  {
    $this->response[$offset] = $value;
  }

  // 2
  public function offsetExists($offset) 
  {
    return isset($this->response[$offset]);
  }
  
  // 3
  public function offsetUnset($offset) 
  {
    unset($this->response[$offset]);
  }

  // 4
  public function offsetGet($offset) 
  {
    return isset($this->response[$offset]) ? $this->response[$offset] : null;
  }

  public function __get($name)
  {
    if($this->__resp->code != 200)
      EpiOAuthException::raise($this->__resp->data, $this->__resp->code);

    $this->responseText = $this->__resp->data;
    $this->response     = json_decode($this->responseText, 1);
    $this->__obj        = json_decode($this->responseText);
    foreach($this->__obj as $k => $v)
    {
      $this->$k = $v;
    }

    return $this->$name;
  }

  public function __isset($name)
  {
    $value = self::__get($name);
    return empty($name);
  }
}
