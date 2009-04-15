<?php
require_once '../EpiCurl.php';
require_once '../EpiOAuth.php';
require_once '../EpiTwitter.php';
require_once 'PHPUnit/Framework.php';

class EpiTwitterTest extends PHPUnit_Framework_TestCase
{
  function setUp()
  {
    // key and secret for a test app (don't really care if this is public)
    $consumer_key = 'jdv3dsDhsYuJRlZFSuI2fg';
    $consumer_secret = 'NNXamBsBFG8PnEmacYs0uCtbtsz346OJSod7Dl94';
    $token = '25451974-uakRmTZxrSFQbkDjZnTAsxDO5o9kacz2LT6kqEHA';
    $secret= 'CuQPQ1WqIdSJDTIkDUlXjHpbcRao9lcKhQHflqGE8';
    $this->twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $token, $secret);
  }

  function testGetVerifyCredentials()
  {
    $resp = $this->twitterObj->get_accountVerify_credentials();
    $responseText = $resp->responseText;
    $responseArray = $resp->response;
    $screen_name = $resp->screen_name;
    $this->assertTrue(!empty($responseText), 'responseText was empty');
    $this->assertTrue($resp instanceof EpiTwitterJson, 'response is not an array');
    $this->assertTrue(!empty($screen_name), 'member property screen_name is empty');
  }

  function testGetFollowers()
  {
    $resp = $this->twitterObj->get_statusesFollowers();
    $this->assertTrue(count($resp) > 0, 'Count of followers is not greater than 0');
    $this->assertTrue(!empty($resp[0]), 'array access for resp is empty');
    foreach($resp as $k => $v)
    {
      $this->assertTrue(!empty($v->screen_name), 'screen name for one of the resp nodes is empty');
    }
    $this->assertTrue($k > 0, 'test did not properly loop over followers');
  }

  function testPostStatus()
  {
    $statusText = 'Testing a random status (' . time() . ')';
    $resp = $this->twitterObj->post_statusesUpdate(array('status' => $statusText));
    $newStatus = $resp->text;
    $this->assertEquals($newStatus, $statusText, 'The status was not updated correctly');
  }
}
