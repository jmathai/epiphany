<?php
class EpiTwitter extends EpiOAuth
{
  protected $requestTokenUrl = 'http://twitter.com/oauth/request_token';
  protected $accessTokenUrl = 'http://twitter.com/oauth/access_token';
  protected $authorizeUrl = 'http://twitter.com/oauth/authorize';
  protected $apiUrl = 'http://twitter.com/oauth/authorize';

  public function getUserInfo()
  {
    return new EpiTwitterJson($this->httpRequest('POST', 'http://twitter.com/account/verify_credentials.json'));
  }

  public function setUserStatus($status = null/*, $in_reply_to_status_id = null*/)
  {
    if(empty($status) || strlen($status) > 140)
      return false;

    return new EpiTwitterJson($this->httpRequest('POST', 'http://twitter.com/statuses/update.json', array('status' => $status)));
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
  private $resp;

  public function __construct($resp)
  {
    $this->resp = $resp;
  }

  public function __get($name)
  {
    var_dump($this->resp->data);
    if($this->resp->code < 200 || $this->resp->code > 299)
      return false;

    $result = json_decode($this->resp->data, 1);
    foreach($result as $k => $v)
    {
      $this->$k = $v;
    }

    return $result[$name];
  }
}
?>
