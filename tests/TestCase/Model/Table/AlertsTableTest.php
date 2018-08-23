<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AlertsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AlertsTable Test Case
 */
class AlertsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\AlertsTable
     */
    public $Alerts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.alerts'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Alerts') ? [] : ['className' => AlertsTable::class];
        $this->Alerts = TableRegistry::get('Alerts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Alerts);

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
}
