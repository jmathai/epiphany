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
    $this->assertTrue(is_array($responseArray), 'response is not an array');
    $this->assertTrue(!empty($screen_name), 'member property screen_name is empty');
  }

  function testGetWithParameters()
  {
    $resp = $this->twitterObj->get_statusesFriends_timeline(array('since_id' => 1));
    $this->assertTrue(!empty($resp->response[0]['user']['screen_name']), 'first status has no screen name');
  }

  function testPostWithParameters()
  {
    $statusText = 'Testing a random status (' . time() . ')';
    $resp = $this->twitterObj->post_statusesUpdate(array('status' => $statusText));
    $newStatus = $resp->response['text'];
    $this->assertEquals($newStatus, $statusText, 'The status was not updated correctly');
  }

  function testSearch()
  {
    $resp = $this->twitterObj->get_search(array('q' => 'hello'));
    $this->assertTrue(!empty($resp->response['results']));
  }

  function testTrends()
  {
    $resp = $this->twitterObj->get_trends();
    $this->assertTrue(!empty($resp->response['trends']));

    $resp = $this->twitterObj->get_trendsCurrent();
    $this->assertTrue(!empty($resp->response['trends']));
  }
}
