<?php
/**
 * @file
 */
namespace Discovery;


class MetaTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var array
   */
  protected $meta;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->meta = array(
      'good' => array(
        'type' => 'type',
        'subtype' => 'subtype',
        'null' => NULL,
        'value' => 'value',
        'array' => array(
          'test',
        ),
        'object' => (object)array(
          'property' => 'property',
        ),
      ),
      'bad_type' => array(
        'subtype' => 'subtype',
        'missing' => 'types',
      ),
      'bad_subtype' => array(
        'type' => 'type',
      ),
    );
  }

  /**
   *
   */
  public function testGoodConstruct() {
    $meta = new Meta('Test\\Class', $this->meta['good']);
    $this->assertEquals($meta->getClass(), 'Test\\Class');
  }

  /**
   *
   */
  public function testBadType() {
    $this->setExpectedException('InvalidArgumentException');
    new Meta('Test\\Class', $this->meta['bad_type']);
  }

  /**
   *
   */
  public function testBadSubtype() {
    $this->setExpectedException('InvalidArgumentException');
    new Meta('Test\\Class', $this->meta['bad_subtype']);
  }

  /**
   *
   */
  public function testGetValue() {
    $meta = new Meta('Test\\Class', $this->meta['good']);
    $this->assertEquals($meta->getValue('type'), 'type');
    $this->assertEquals($meta->getValue('subtype'), 'subtype');
    $this->assertEquals($meta->getValue('null'), NULL);
    $this->assertEquals($meta->getValue('array'), array('test'));
    $this->assertEquals($meta->getValue('object'), (object)array('property' => 'property'));
  }
}
