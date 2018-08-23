<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\IpaddressesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\IpaddressesTable Test Case
 */
class IpaddressesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\IpaddressesTable
     */
    public $Ipaddresses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ipaddresses',
        'app.posts'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Ipaddresses') ? [] : ['className' => IpaddressesTable::class];
        $this->Ipaddresses = TableRegistry::get('Ipaddresses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Ipaddresses);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
