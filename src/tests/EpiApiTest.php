<?php
class EpiApiTest extends PHPUnit_Framework_TestCase
{
  public static $random;
  private $apiObj, $routeObj;
  public function setUp()
  {
    self::$random = uniqid();
    Epi::setPath('base', SRC_DIR);
    Epi::setSetting('exceptions', true);
    Epi::init('api');
    $this->apiObj = getApi();
    $this->routeObj = getRoute();
  }

  public function testEpiApiExists()
  {
    $this->assertTrue(class_exists('EpiApi'));
  }

  public function testGetRoute()
  {
    $this->apiObj->get(__FUNCTION__, 'unittestapicallback');
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = __FUNCTION__;
    $routeDef = $this->apiObj->getRoute(__FUNCTION__, EpiRoute::httpGet);
    $this->assertEquals($routeDef['callback'], 'unittestapicallback');
  }

  public function testOptionsRoute()
  {
    $this->apiObj->options(__FUNCTION__, 'unittestapicallback');
    $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
    $_SERVER['REQUEST_URI'] = __FUNCTION__;
    $routeDef = $this->apiObj->getRoute(__FUNCTION__, EpiRoute::httpOptions);
    $this->assertEquals($routeDef['callback'], 'unittestapicallback');
  }

  public function testPostRoute()
  {
    $this->apiObj->post(__FUNCTION__, 'unittestapicallback');
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = __FUNCTION__;
    $routeDef = $this->apiObj->getRoute(__FUNCTION__, EpiRoute::httpPost);
    $this->assertEquals($routeDef['callback'], 'unittestapicallback');
  }

  public function testPublicRouteNotPubliclyAccessible()
  {
    $this->apiObj->get(__FUNCTION__, 'unittestapicallback', EpiApi::external);
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = __FUNCTION__;
    $routeDef = $this->routeObj->getRoute(__FUNCTION__, EpiRoute::httpGet);
    $this->assertEquals($routeDef['callback'], 'unittestapicallback');
  }

  /**
   * @expectedException EpiRouteNotFoundException
   */
  public function testPrivateRouteNotPubliclyAccessible()
  {
    $this->apiObj->get(__FUNCTION__, 'unittestapicallback', EpiApi::internal);
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = __FUNCTION__;
    $routeDef = $this->routeObj->getRoute(__FUNCTION__, EpiRoute::httpGet);
  }

  /**
   * @expectedException EpiRouteNotFoundException
   */
  public function testDefaultVisibilityIsPrivate()
  {
    $this->apiObj->get(__FUNCTION__, 'unittestapicallback'/*, EpiApi::internal*/);
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = __FUNCTION__;
    $routeDef = $this->routeObj->getRoute(__FUNCTION__, EpiRoute::httpGet);
  }

  public function testInvoke()
  {
    $this->apiObj->get(__FUNCTION__, 'unittestapicallback');
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = __FUNCTION__;
    $result = $this->apiObj->invoke(__FUNCTION__);
    $this->assertEquals($result, EpiApiTest::$random);
  }
}

function unittestapicallback()
{
  return EpiApiTest::$random;
}
