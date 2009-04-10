<?php
class EpiTwitter extends EpiOAuth
{
  protected $requestTokenUrl = 'http://twitter.com/oauth/request_token';
  protected $accessTokenUrl = 'http://twitter.com/oauth/access_token';
  protected $authorizeUrl = 'http://twitter.com/oauth/authorize';
  protected $apiUrl = 'http://twitter.com';

  public function __call($name, $params = null)
  {
    $parts  = explode('_', $name);
    $method = strtoupper(array_shift($parts));
    $parts  = implode('_', $parts);
    $url    = $this->apiUrl . '/' . preg_replace('/[A-Z]|[0-9]+/e', "'/'.strtolower('\\0')", $parts) . '.json';
    if(!empty($params))
      $args = array_shift($params);

    return EpiTwitterJson::create (call_user_func(array($this, 'httpRequest'), $method, $url, $args));
  }

  public function __construct($consumerKey = null, $consumerSecret = null, $oauthToken = null, $oauthTokenSecret = null)
  {
    $this->consumerKey = $consumerKey;
    $this->consumerSecret = $consumerSecret;
    $this->signatureMethod = 'HMAC-SHA1';
    $this->setToken($oauthToken, $oauthTokenSecret);
    parent::__construct();
  }
}

class EpiTwitterJson
{
  protected $httpResponse;
	public $jsonResponse;
	public $response;
	
  public function __construct($httpResponse, $jsonResponse, $response)
  {
    $this->httpResponse = $httpResponse;
		$this->jsonResponse = $jsonResponse;
		$this->response = $response;
  }

	public function isValid ()
	{
		if($this->httpResponse->code < 200 || $this->httpResponse->code > 299)
      return false;
	}

	public static function create ($httpResponse) 
	{
		$response = json_decode ($httpResponse->data, false);
		if (is_array($response))
		{
			return new EpiTwitterJsonArray ($httpResponse, $httpResponse->data, $response);
		}
		else 
		{
			return new EpiTwitterJson ($httpResponse, $httpResponse->data, $response);
		}
	}

  public function __get($name)
  {
		if (isset($this->response->$name))
		{
			return $this->response->$name;
		}
		return false;
  }
}

class EpiTwitterJsonArray extends EpiTwitterJson implements ArrayAccess, Countable,  IteratorAggregate
{
	private $repsonse = array();

	// Implementation of the IteratorAggregate::getIterator() to support foreach ($this as $...)
	public function getIterator ()
	{
		return new ArrayIterator($this->response);
	}

	// Implementation of Countable::count() to support count($this)
	public function count ()
	{
		return count($this->response);
	}
	
	// Next four functions are to support ArrayAccess interface
	public function offsetSet($offset, $value) 
	{
		$this->response[$offset] = $value;
	}
	
	public function offsetExists($offset) 
	{
		return isset($this->response[$offset]);
	}
	
	public function offsetUnset($offset) 
	{
		unset($this->response[$offset]);
	}
	
	public function offsetGet($offset) 
	{
		return isset($this->response[$offset]) ? $this->response[$offset] : null;
	}
}
