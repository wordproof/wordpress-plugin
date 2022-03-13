<?php

namespace WordProofTimestamp\App\Helpers;

class ActionHelper {

	private static $group = 'wordproof';
	private static $hook = 'wordproof_action';

	/**
	 * Schedules an action to be run as soon as possible.
	 *
	 * @param $action
	 * @param array $data
	 */
	static function now( $action, $data = [] ) {

		as_enqueue_async_action( self::$hook, self::getArguments($action, $data), self::$group );

	}

	/**
	 * Schedules an action to be run on a certain time.
	 *
	 * @param $timestamp
	 * @param $action
	 * @param array $data
	 */
	static function later( $timestamp, $action, $data = [] ) {

		as_schedule_single_action( $timestamp, self::$hook, self::getArguments($action, $data), self::$group );

	}

	/**
	 * Returns the arguments for the action scheduler.
	 *
	 * @param $action
	 * @param $data
	 *
	 * @return array
	 */
	static private function getArguments($action, $data) {
		$arguments = array_merge( [ 'action' => $action, ], $data);

		return [ 'data' => $arguments ];
	}
}
