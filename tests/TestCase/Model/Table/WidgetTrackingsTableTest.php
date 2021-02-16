<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WidgetTrackingsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WidgetTrackingsTable Test Case
 */
class WidgetTrackingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\WidgetTrackingsTable
     */
    protected $WidgetTrackings;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.WidgetTrackings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('WidgetTrackings') ? [] : ['className' => WidgetTrackingsTable::class];
        $this->WidgetTrackings = $this->getTableLocator()->get('WidgetTrackings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->WidgetTrackings);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
