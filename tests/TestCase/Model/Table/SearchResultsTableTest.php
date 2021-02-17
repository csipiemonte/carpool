<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SearchResultsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SearchResultsTable Test Case
 */
class SearchResultsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SearchResultsTable
     */
    protected $SearchResults;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.SearchResults',
        'app.Drivers',
        'app.Sessions',
        'app.Providers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SearchResults') ? [] : ['className' => SearchResultsTable::class];
        $this->SearchResults = $this->getTableLocator()->get('SearchResults', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchResults);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
