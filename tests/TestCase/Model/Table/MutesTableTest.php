<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MutesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MutesTable Test Case
 */
class MutesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\MutesTable
     */
    public $Mutes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.mutes',
        'app.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Mutes') ? [] : ['className' => MutesTable::class];
        $this->Mutes = TableRegistry::get('Mutes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Mutes);

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
