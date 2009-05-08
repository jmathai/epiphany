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
    $this->screenName = 'jmathai_test';
  }

  function testBooleanResponse()
  {
    $resp = $this->twitterObj->get_friendshipsExists(array('user_a' => 'jmathai_test','user_b' => 'jmathai'));
    $this->assertTrue(gettype($resp->response) === 'boolean', 'response should be a boolean for friendship exists');
    $this->assertTrue($resp->response, 'response should be true for friendship exists');
  }

  function testGetVerifyCredentials()
  {
    $resp = $this->twitterObj->get_accountVerify_credentials();
    $this->assertTrue(strlen($resp->responseText) > 0, 'responseText was empty');
    $this->assertTrue($resp instanceof EpiTwitterJson, 'response is not an array');
    $this->assertTrue(!empty($resp->screen_name), 'member property screen_name is empty');
  }

  function testGetWithParameters()
  {
    $resp = $this->twitterObj->get_statusesFriends_timeline(array('since_id' => 1));
    $this->assertTrue(!empty($resp->response[0]['user']['screen_name']), 'first status has no screen name');
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
    $statusText = 'Testing a random status (time: ' . time() . ')';
    $resp = $this->twitterObj->post_statusesUpdate(array('status' => $statusText));
    $this->assertEquals($resp->text, $statusText, 'The status was not updated correctly');
    // reply to it
    $statusText = 'Testing a random status with reply to id (reply to: ' . $resp->id . ')';
    $resp = $this->twitterObj->post_statusesUpdate(array('status' => $statusText, 'in_reply_to_status_id' => $resp->id));
    $this->assertEquals($resp->text, $statusText, 'The status with reply to id was not updated correctly');
  }

  function testPostStatusUnicode()
  {
    $statusText = 'Testing a random status with unicode בוקר טוב (' . time() . ')';
    $resp = $this->twitterObj->post_statusesUpdate(array('status' => $statusText));
    $this->assertEquals($resp->text, $statusText, 'The status was not updated correctly');
  }

  function testDirectMessage()
  {
    $resp = $this->twitterObj->post_direct_messagesNew( array ( 'user' => $this->screenName, 'text' => "@username that's dirt cheap man, good looking out. I shall buy soon.You still play Halo at all?"));
    $this->assertTrue(!empty($resp->response['id']), "response id is empty");
  }

  function testPassingInTokenParams()
  {
    $this->twitterObj->setToken(null, null);
    $token = $this->twitterObj->getRequestToken();
    $authenticateUrl = $this->twitterObj->getAuthorizationUrl($token);
    $this->assertEquals($token->oauth_token, substr($authenticateUrl, (strpos($authenticateUrl, '=')+1)), "token does not equal the one which was passed in");
  }

  /**
  * @expectedException EpiOAuthException
  */
  function testNoRequiredParameter()
  {
    $resp = $this->twitterObj->post_direct_messagesNew( array ( 'user' => $this->screenName, 'text' => ''));
    $this->assertTrue(!empty($resp->response['error']), "An empty direct message should return an error message");

  }

  function testResponseAccess()
  {
    $resp = $this->twitterObj->get_statusesFollowers();
    $this->assertTrue(!empty($resp[0]), 'array access for resp is empty');
    $this->assertEquals($resp[0], $resp->response[0], 'array access for resp is empty');
    foreach($resp as $k => $v)
    {
      $this->assertTrue(!empty($v->screen_name), 'screen name for one of the resp nodes is empty');
    }
    $this->assertTrue($k > 0, 'test did not properly loop over followers');
  }

  function testSearch()
  {
    $resp = $this->twitterObj->get_search(array('q' => 'hello'));
    $this->assertTrue(is_array($resp->response['results']));
    $this->assertTrue(!empty($resp->results[0]->text), "search response is not an array {$resp->results[0]->text}");
  }

  function testTrends()
  {
    $resp = $this->twitterObj->get_trends();
    $this->assertTrue(is_array($resp->response['trends']), "trends is empty");
    $this->assertTrue(!empty($resp->trends[0]->name), "current trends is not an array " . $resp->trends[0]->name);

    $resp = $this->twitterObj->get_trendsCurrent();
    $this->assertTrue(is_array($resp->response['trends']), "current trends is empty");
  }

  function testSSl()
  {
    $this->twitterObj->useSSL(true);
    $resp = $this->twitterObj->get_accountVerify_credentials();
    $this->assertTrue(strlen($resp->responseText) > 0, 'responseText was empty');
    $this->assertTrue($resp instanceof EpiTwitterJson, 'response is not an array');
    $this->assertTrue(!empty($resp->screen_name), 'member property screen_name is empty');
    $this->twitterObj->useSSL(false);
  }
}
