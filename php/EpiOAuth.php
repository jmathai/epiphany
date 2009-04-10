<?php
class EpiOAuth
{
  public $version = '1.0';

  protected $requestTokenUrl;
  protected $accessTokenUrl;
  protected $authorizeUrl;
  protected $consumerKey;
  protected $consumerSecret;
  protected $token;
  protected $tokenSecret;
  protected $signatureMethod;

  public function getAccessToken()
  {
    $resp = $this->httpRequest('GET', $this->accessTokenUrl);
    return new EpiOAuthResponse($resp);
  }

  public function getAuthorizationUrl()
  { 
    $retval = "{$this->authorizeUrl}?";

    $token = $this->getRequestToken();
    return $this->authorizeUrl . '?oauth_token=' . $token->oauth_token;
  }

  public function getRequestToken()
  {
    $resp = $this->httpRequest('GET', $this->requestTokenUrl);
    return new EpiOAuthResponse($resp);
  }

  public function httpRequest($method = null, $url = null, $params = null)
  {
    if(empty($method) || empty($url))
      return false;

    if(empty($params['oauth_signature']))
      $params = $this->prepareParameters($method, $url, $params);

    switch($method)
    {
      case 'GET':
        return $this->httpGet($url, $params);
        break;
      case 'POST':
        return $this->httpPost($url, $params);
        break;
    }
  }

  final public function setToken($token = null, $secret = null)
  {
    $params = func_get_args();
    $this->token = $token;
    $this->tokenSecret = $secret;
  } 

  final public function encode($string)
  {
    return rawurlencode(utf8_encode($string));
  }

  final private function generateNonce()
  {
    return md5(uniqid(rand(), true));
  }

  final private function generateSignature($method = null, $url = null, $params = null)
  {
    if(empty($method) || empty($url))
      return false;


    // concatenating
    $concatenatedParams = '';
    foreach($params as $k => $v)
    {
      $v = $this->encode($v);
      $concatenatedParams .= "{$k}={$v}&";
    }
    $concatenatedParams = $this->encode(substr($concatenatedParams, 0, -1));

    // normalize url
    $normalizedUrl = $this->encode($this->normalizeUrl($url));
    $method = $this->encode($method); // don't need this but why not?

    $signatureBaseString = "{$method}&{$normalizedUrl}&{$concatenatedParams}";
    
    return $this->signString($signatureBaseString);
  }

  final private function httpGet($url, $params = null)
  {
    if(count($params) > 0)
    {
      $url .= '?';
      foreach($params as $k => $v)
      {
        $url .= "{$k}={$v}&";
      }
      $url = substr($url, 0, -1);
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
    $resp  = $this->curl->addCurl($ch);

    return $resp;
  }

  final private function httpPost($url, $params = null)
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
    $resp  = $this->curl->addCurl($ch);

    return $resp;
  }

  final private function normalizeUrl($url = null)
  {
    $urlParts = parse_url($url);
    $scheme = strtolower($urlParts['scheme']);
    $host   = strtolower($urlParts['host']);
    $port = intval($urlParts['port']);

    $retval = "{$scheme}://{$host}";
    if($port > 0 && ($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443))
    {
      $retval .= ":{$port}";
    }
    $retval .= $urlParts['path'];
    if(!empty($urlParts['query']))
    {
      $retval .= "?{$urlParts['query']}";
    }

    return $retval;
  }

  final public function prepareParameters($method = null, $url = null, $params = null)
  {
    if(empty($method) || empty($url))
      return false;

    $params['oauth_consumer_key'] = $this->consumerKey;
    $params['oauth_token'] = $this->token;
    $params['oauth_nonce'] = $this->generateNonce();
    $params['oauth_timestamp'] = time();;
    $params['oauth_signature_method'] = $this->signatureMethod;
    $params['oauth_version'] = $this->version;

    // encoding
    $encodedParams = array();
    foreach($params as $k => $v)
    {
      if(strstr($k, 'oauth_'))
        $encodedParams[$this->encode($k)] = $this->encode($v);
      else
        $encodedParams[$this->encode($k)] = utf8_encode($v);
    }

    // sorting
    ksort($encodedParams);

    // signing
    $encodedParams['oauth_signature'] = $this->generateSignature($method, $url, $encodedParams);
    return $encodedParams;
  }

  final private function signString($string = null)
  {
    $retval = false;
    switch($this->signatureMethod)
    {
      case 'HMAC-SHA1':
        $key = $this->encode($this->consumerSecret) . '&' . $this->encode($this->tokenSecret);
        $retval = $this->encode(base64_encode(hash_hmac('sha1', $string, $key, true)));
        break;
    }

    return $retval;
  }

  public function __construct()
  {
    $this->curl = EpiCurl::getInstance();
  }
}

class EpiOAuthResponse
{
  private $__resp;

  public function __construct($resp)
  {
    $this->__resp = $resp;
  }

  public function __get($name)
  {
    if($this->__resp->code < 200 || $this->__resp->code > 299)
      return false;

    parse_str($this->__resp->data, $result);
    foreach($result as $k => $v)
    {
      $this->$k = $v;
    }

    return $result[$name];
  }
}
