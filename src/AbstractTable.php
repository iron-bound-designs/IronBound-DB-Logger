<?php
/**
 * Abstract table for logs.
 *
 * @author    Iron Bound Designs
 * @since     1.0
 * @license   MIT
 * @copyright Iron Bound Designs, 2015.
 */

namespace IronBound\DBLogger;

use IronBound\DB\Table\BaseTable;
use IronBound\DB\Table\Column\DateTime;
use IronBound\DB\Table\Column\ForeignUser;
use IronBound\DB\Table\Column\IntegerBased;
use IronBound\DB\Table\Column\StringBased;

/**
 * Class AbstractTable
 *
 * @package IronBound\DBLogger
 */
abstract class AbstractTable extends BaseTable {

	/**
	 * Columns in the table.
	 *
	 * key => sprintf field type
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'id'        => new IntegerBased( 'BIGINT', 'id', array( 'NOT NULL', 'auto_increment' ), array( 20 ) ),
			'message'   => new StringBased( 'VARCHAR', 'message', array(), array( 255 ) ),
			'level'     => new StringBased( 'VARCHAR', 'level', array(), array( 20 ) ),
			'lgroup'    => new StringBased( 'VARCHAR', 'lgroup', array(), array( 20 ) ),
			'time'      => new DateTime( 'time' ),
			'user'      => new ForeignUser( 'user' ),
			'ip'        => new StringBased( 'VARCHAR', 'ip', array(), array( 45 ) ),
			'exception' => new StringBased( 'VARCHAR', 'exception', array(), array( 255 ) ),
			'trace'     => new StringBased( 'LONGTEXT', 'trace' ),
			'context'   => new StringBased( 'LONGTEXT', 'context' ),
		);
	}

	/**
	 * Default column values.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_column_defaults() {
		return array(
			'id'        => '',
			'message'   => '',
			'level'     => '',
			'lgroup'    => '',
			'time'      => '',
			'user'      => '',
			'ip'        => '',
			'exception' => '',
			'trace'     => '',
			'context'   => ''
		);
	}

	/**
	 * Retrieve the name of the primary key.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_primary_key() {
		return 'id';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_keys() {
		$keys = parent::get_keys();

		$keys[] = 'KEY lgroup (lgroup)';
		$keys[] = 'KEY user (user)';

		return $keys;
	}
}