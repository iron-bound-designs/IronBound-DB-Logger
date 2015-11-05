<?php
/**
 * Abstract log model.
 *
 * @author    Iron Bound Designs
 * @since     1.0
 * @license   MIT
 * @copyright Iron Bound Designs, 2015.
 */

namespace IronBound\DBLogger;

use IronBound\DB\Model;

/**
 * Class Log
 * @package IronBound\DBLogger
 *
 * This must be extended for each log type, and override the get_table() method.
 */
abstract class AbstractLog extends Model {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $level;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var string
	 */
	private $group;

	/**
	 * @var \DateTime|null
	 */
	private $time;

	/**
	 * @var string
	 */
	private $ip;

	/**
	 * @var string
	 */
	private $exception;

	/**
	 * @var string
	 */
	private $trace;

	/**
	 * @var array
	 */
	private $context;

	/**
	 * Log constructor.
	 *
	 * @param \stdClass $data
	 */
	public function __construct( \stdClass $data ) {
		$this->init( $data );
	}

	/**
	 * Init an object.
	 *
	 * @since 1.0
	 *
	 * @param \stdClass $data
	 */
	protected function init( \stdClass $data ) {

		$this->id        = $data->id;
		$this->level     = $data->level;
		$this->message   = $data->message;
		$this->group     = $data->lgroup;
		$this->time      = empty( $data->time ) ? null : new \DateTime( $data->time );
		$this->ip        = empty( $data->ip ) ? '' : inet_ntop( $data->ip );
		$this->exception = $data->exception;
		$this->trace     = $data->trace;
		$this->context   = json_decode( $data->context );
	}

	/**
	 * Get the unique pk for this record.
	 *
	 * @since 1.0
	 *
	 * @return mixed (generally int, but not necessarily).
	 */
	public function get_pk() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_level() {
		return $this->level;
	}

	/**
	 * @return string
	 */
	public function get_message() {
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * @return \DateTime|null
	 */
	public function get_time() {
		return $this->time;
	}

	/**
	 * @return string
	 */
	public function get_ip() {
		return $this->ip;
	}

	/**
	 * @return string
	 */
	public function get_exception() {
		return $this->exception;
	}

	/**
	 * @return string
	 */
	public function get_trace() {
		return $this->trace;
	}

	/**
	 * @return array
	 */
	public function get_context() {
		return $this->context;
	}
}
