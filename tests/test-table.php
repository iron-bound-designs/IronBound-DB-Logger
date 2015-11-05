<?php
/**
 * Test the concrete table.
 *
 * @author    Iron Bound Designs
 * @since     1.0
 * @license   MIT
 * @copyright Iron Bound Designs, 2015.
 */

namespace IronBound\DBLogger\Tests;

use IronBound\DBLogger\Table;

/**
 * Class Test_Table
 * @package IronBound\DBLogger\Tests
 */
class Test_Table extends \WP_UnitTestCase {

	public function test_slug() {

		$table = new Table( 'my-slug' );

		$this->assertEquals( 'my-slug', $table->get_slug() );
	}
}