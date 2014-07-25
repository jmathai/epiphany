<?php
class EpiTest extends PHPUnit_Framework_TestCase
{
  public function setUp() { }

  /**
   * @expectedException EpiDependencyException
   */
  public function testInitNoPathSet()
  {
    Epi::init('route');
  }

  public function testInitSuccess()
  {
    Epi::setPath('base', SRC_DIR);
    Epi::init('route');
    $this->assertTrue(class_exists('EpiRoute'));
  }

  public function testSetPathUnset()
  {
    $actualValue = Epi::getPath('DNE');
    $this->assertNull($actualValue);
  }

  public function testSetPathSuccess()
  {
    $name = 'path';
    $value = time();
    Epi::setPath($name, $value);
    $actualValue = Epi::getPath($name);

    $this->assertEquals($value, $actualValue);
  }

  public function testSetSettingUnset()
  {
    $actualValue = Epi::getSetting('DNE');
    $this->assertFalse($actualValue);
  }

  public function testSetSettingSuccess()
  {
    $name = 'setting';
    $value = time();
    Epi::setSetting($name, $value);
    $actualValue = Epi::getSetting($name);

    $this->assertEquals($value, $actualValue);
  }
}
