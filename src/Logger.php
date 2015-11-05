<?php
/**
 * Logger utilizing an ironbound\db table
 *
 * @author    Iron Bound Designs
 * @since     1.0
 * @license   MIT
 * @copyright Iron Bound Designs, 2015.
 */

namespace IronBound\DBLogger;

use IronBound\DB\Query\Simple_Query;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

/**
 * Class Logger
 * @package IronBound\DBLogger
 */
class Logger extends AbstractLogger {

	/**
	 * @var Simple_Query
	 */
	private $query;

	/**
	 * @var AbstractTable
	 */
	private $table;

	/**
	 * Logger constructor.
	 *
	 * @param AbstractTable $table
	 * @param \wpdb         $wpdb
	 */
	public function __construct( AbstractTable $table, \wpdb $wpdb ) {
		$this->table = $table;
		$this->query = new Simple_Query( $wpdb, $table );
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * The current user ID will be automatically logged.
	 * To bypass this pass `false` for `_user` in `$context`.
	 *
	 * To assign the log to a group, pass the group name ( up to 20 chars ) for `_group` in `$context`.
	 *
	 * Any additional columns in the db can be passed in `$context` by prefixing the column name with `_`.
	 * For example, passing a key `_product_id` in `$context` will save whatever value given in the column
	 * named `product_id`. If the column does not exist the value will be silently discarded.
	 *
	 * @param mixed  $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @return null
	 */
	public function log( $level, $message, array $context = array() ) {

		if ( ! in_array( $level, array(
			LogLevel::ALERT,
			LogLevel::CRITICAL,
			LogLevel::DEBUG,
			LogLevel::EMERGENCY,
			LogLevel::ERROR,
			LogLevel::INFO,
			LogLevel::DEBUG,
			LogLevel::WARNING
		) )
		) {
			throw new InvalidArgumentException( sprintf( "Invalid log level '%s'", $level ) );
		}

		$message = (string) $message;
		$message = $this->interpolate( $message, $context );

		if ( isset( $context['exception'] ) && $context['exception'] instanceof \Exception ) {

			/** @var \Exception $exception */
			$exception = $context['exception'];

			$class = get_class( $context['exception'] );
			$trace = $exception->getTraceAsString();
		} else {
			$class = '';
			$trace = '';
		}

		$data = array(
			'message'   => $message,
			'lgroup'    => isset( $context['_group'] ) ? substr( $context['_group'], 0, 20 ) : '',
			'time'      => date( 'Y-m-d H:i:s' ),
			'ip'        => inet_pton( $this->get_ip() ),
			'exception' => $class,
			'trace'     => $trace
		);

		$custom_columns = $this->table->get_columns();

		foreach ( $context as $key => $value ) {

			if ( strpos( $key, '_' ) === 0 ) {

				$context_column_name = substr( $key, 1 );

				if ( isset( $custom_columns[ $context_column_name ] ) ) {
					$data[ $context_column_name ] = $value;
				}
			}
		}

		if ( ! isset( $data['user'] ) && is_user_logged_in() ) {
			$data['user'] = get_current_user_id();
		}

		$this->query->insert( $data );
	}

	/**
	 * Interpolates context values into the message placeholders.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return string
	 */
	protected function interpolate( $message, array $context = array() ) {

		// build a replacement array with braces around the context keys
		$replace = array();

		foreach ( $context as $key => $val ) {
			$replace[ '{' . $key . '}' ] = (string) $val;
		}

		// interpolate replacement values into the message and return
		return strtr( $message, $replace );
	}

	/**
	 * Get the current IP address.
	 *
	 * @link  http://stackoverflow.com/a/19189952
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	protected function get_ip() {

		return getenv( 'HTTP_CLIENT_IP' ) ?:
			getenv( 'HTTP_X_FORWARDED_FOR' ) ?:
				getenv( 'HTTP_X_FORWARDED' ) ?:
					getenv( 'HTTP_FORWARDED_FOR' ) ?:
						getenv( 'HTTP_FORWARDED' ) ?:
							getenv( 'REMOTE_ADDR' );
	}
}