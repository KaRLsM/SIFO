<?php
ini_set("include_path", "../libs/SEOframework".PATH_SEPARATOR."../../../libs/SEOframework".PATH_SEPARATOR.ini_get("include_path"));
require_once 'PHPUnit/Framework.php';

require_once '/var/www/seoframework/libs/SEOframework/Cache.php';

/**
 * Test class for Cache.
 * Generated by PHPUnit on 2009-11-01 at 12:17:06.
 */
class CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Cache;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testGetInstance().
     */
    public function testGetInstance()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement test__call().
     */
    public function test__call()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>