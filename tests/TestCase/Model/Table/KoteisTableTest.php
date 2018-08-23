<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KoteisTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KoteisTable Test Case
 */
class KoteisTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\KoteisTable
     */
    public $Koteis;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.koteis',
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
        $config = TableRegistry::exists('Koteis') ? [] : ['className' => KoteisTable::class];
        $this->Koteis = TableRegistry::get('Koteis', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Koteis);

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
