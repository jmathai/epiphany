<?php
class EpiTwitter extends EpiOAuth
{
  const EPITWITTER_SIGNATURE_METHOD = 'HMAC-SHA1';
  protected $requestTokenUrl= 'http://twitter.com/oauth/request_token';
  protected $accessTokenUrl = 'http://twitter.com/oauth/access_token';
  protected $authorizeUrl   = 'http://twitter.com/oauth/authorize';
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

class EpiTwitterJson
{
  private $resp;

  public function __construct($resp)
  {
    $this->resp = $resp;
  }

  public function __get($name)
  {
    $this->responseText = $this->resp->data;
    $this->response = (array)json_decode($this->responseText, 1);
    foreach($this->response as $k => $v)
    {
      $this->$k = $v;
    }

    return $this->$name;
  }
}
