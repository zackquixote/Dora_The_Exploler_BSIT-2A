<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class AdvancedFeaturesTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testApiSearchHealthRecords()
    {
        $result = $this->withURI('http://localhost/advanced/api/health-records/search')
                       ->controller(\App\Controllers\AdvancedFeatures::class)
                       ->execute('apiSearchHealthRecords');
        
        $this->assertTrue($result->isOK());
        $result->assertJSONExact(['success' => true, 'data' => []]);
    }

    public function testApiSearchBusiness()
    {
        $result = $this->withURI('http://localhost/advanced/api/business/search')
                       ->controller(\App\Controllers\AdvancedFeatures::class)
                       ->execute('apiSearchBusiness');
        
        $this->assertTrue($result->isOK());
        $result->assertJSONExact(['success' => true, 'data' => []]);
    }

    public function testApiActiveEmergencies()
    {
        $result = $this->withURI('http://localhost/advanced/api/emergency/active')
                       ->controller(\App\Controllers\AdvancedFeatures::class)
                       ->execute('apiActiveEmergencies');
        
        $this->assertTrue($result->isOK());
        $result->assertJSONExact(['success' => true, 'data' => []]);
    }

    public function testApiEventsList()
    {
        $result = $this->withURI('http://localhost/advanced/api/events/list')
                       ->controller(\App\Controllers\AdvancedFeatures::class)
                       ->execute('apiEventsList');
        
        $this->assertTrue($result->isOK());
        $result->assertJSONExact(['success' => true, 'data' => []]);
    }
}
