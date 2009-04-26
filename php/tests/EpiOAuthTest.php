<?php
require_once '../EpiCurl.php';
require_once '../EpiOAuth.php';
require_once 'PHPUnit/Framework.php';

class OAuthHarness extends EpiOAuth
{
  public function __construct($consumer_key, $consumer_secret)
  {
    parent::__construct($consumer_key, $consumer_secret);
  }

  public function myPrepareParameters($method, $url, $params)
  {
    return $this->prepareParameters($method, $url, $params);
  }

  public function myUnencode($string)
  {
    return utf8_decode(rawurldecode($string));
  }
}

class EpiTwitterTest extends PHPUnit_Framework_TestCase
{
  function setUp()
  {
    // key and secret for a test app (don't really care if this is public)
    $consumer_key = 'dpf43f3p2l4k3l03';
    $consumer_secret = 'kd94hf93k423kf44';
    $token = 'nnch734d00sl2jdk';
    $secret= 'pfkkdhi9sl3r4s00';
    $this->oauthObj = new OAuthHarness($consumer_key, $consumer_secret);
    $this->oauthObj->setToken($token, $secret);
    $this->oauthObj->apiUrl = 'http://photos.example.net';
    $this->oauthObj->nonce = 'kllo9940pd9333jh';
    $this->oauthObj->timestamp = '1191242096';
  }

  function testGetSignature()
  {
    $parameters = $this->oauthObj->myPrepareParameters('GET', 'http://photos.example.net/photos', array('size'=>'original','file'=>'vacation.jpg'));
    $this->assertEquals(count($parameters), 2, 'prepareParameters did not return a 2 element array');
    $this->assertEquals($this->oauthObj->myUnencode($parameters['oauth']['oauth_signature']), 'tR3+Ty81lMeYAr/Fid0kMTYa/WM=', 'OAuth signature did not match expected value');
  }
  

  function testGetSignatureUnicode()
  {
    $parameters = $this->oauthObj->myPrepareParameters('GET', 'http://photos.example.net/photos', array('size'=>'original','file'=>'vacation.jpg', 'unicode' => 'בוקר טוב'));
    $this->assertEquals(count($parameters), 2, 'prepareParameters did not return a 2 element array');
    $this->assertEquals($this->oauthObj->myUnencode($parameters['oauth']['oauth_signature']), 'ccz61+NlB1dtb3Yf/VFqTN7H2QI=', 'OAuth signature did not match expected value');
  }

  function testPostSignature()
  {
    $parameters = $this->oauthObj->myPrepareParameters('POST', 'http://photos.example.net/photos', array('size'=>'original','file'=>'vacation.jpg'));
    $this->assertEquals(count($parameters), 2, 'prepareParameters did not return a 2 element array');
    $this->assertEquals($this->oauthObj->myUnencode($parameters['oauth']['oauth_signature']), 'wPkvxykrw+BTdCcGqKr+3I+PsiM=', 'OAuth signature did not match expected value');
  }

  function testPostSignatureUnicode()
  {
    $parameters = $this->oauthObj->myPrepareParameters('POST', 'http://photos.example.net/photos', array('size'=>'original','file'=>'vacation.jpg', 'unicode' => 'בוקר טוב'));
    $this->assertEquals(count($parameters), 2, 'prepareParameters did not return a 2 element array');
    $this->assertEquals($this->oauthObj->myUnencode($parameters['oauth']['oauth_signature']), '34jC8oBml1QYKa8/pSBoavRr+Ek=', 'OAuth signature did not match expected value');
  }
}
