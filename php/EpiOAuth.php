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
    $resp = $this->httpRequest('POST', $this->accessTokenUrl);
    return new EpiOAuthResponse($resp);
  }

  public function getAuthorizationLink()
  { 
    $retval = "{$this->authorizeUrl}?";

    $token = $this->getRequestToken();
    $this->setToken($token->oauth_token, $token->oauth_token_secret);
    $params = $this->prepareParameters('GET', $this->authorizeUrl);
    foreach($params as $k => $v)
    {
      $v = $this->encode($v);
      $retval .= "{$k}={$v}&";
    }
    $retval = substr($retval, 0, -1);

    return $retval;
  }

  public function getRequestToken()
  {
    $resp = $this->httpRequest('POST', $this->requestTokenUrl);
    return new EpiOAuthResponse($resp);
  }

  public function httpRequest($method = null, $url = null, $params = null)
  {
    if(empty($method) || empty($url))
      return false;

    if($method === 'GET' && count($params) > 0)
    {
      $url .= '?';
      foreach($params as $k => $v)
      {
        $url .= "{$k}={$v}&";
      }
      $url = substr($url, 0, -1);
    }
    
    if(empty($params['oauth_signature']))
      $params = $this->prepareParameters($method, $url, $params);

    $ch = curl_init($url);
    switch($method)
    {
      case 'POST':
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        break;
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
    //curl_setopt($ch, CURLOPT_HEADER, true); 
    $resp  = $this->curl->addCurl($ch);

    return $resp;
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
        $retval = base64_encode(hash_hmac('sha1', $string, $key, true));
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
  private $resp;

  public function __construct($resp)
  {
    $this->resp = $resp;
  }

  public function __get($name)
  {
    if($this->resp->code < 200 || $this->resp->code > 299)
      return false;

    parse_str($this->resp->data, $result);
    foreach($result as $k => $v)
    {
      $this->$k = $v;
    }

    return $result[$name];
  }
}
?>
