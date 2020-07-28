<?php

namespace DelayCalculator;

use DateInterval;
use DateTime;
use Exception;

/**
 * Class DelayCalculator
 * @package DelayCalculator
 */
class DelayCalculator {

	/**
	 * @param array $rules
	 *
	 * @return DateTime
	 * @throws Exception
	 */
	public function applyDelayWaitTimeDayRuleType(array $rules = []) : DateTime
	{
		// Sanity checks
		if (!isset($rules['amount'])) {
			throw new Exception("Bad amount is given");
		}

		// Create a new queue record with the updated process_at datetime
		try {
			$processAt = new DateTime();
			$processAt->add( new DateInterval( "P{$rules['amount']}D" ) );
		} catch ( \Exception $e ) {
			throw new Exception($e->getMessage());
		}

		// Also, check the time (default is midnight and 1 minute)
		$hour = 0;
		$minute = 1;
		$second = 0;
		if (isset($rules['time'])) {
			if (isset($rules['time']['hours']) && is_numeric($rules['time']['hours'])) {
				// Consider offset
				//$this->considerOffset($rules);
				$hour = $rules['time']['hours'];
			}
			if (isset($rules['time']['minutes']) && is_numeric($rules['time']['minutes'])) {
				$minute = $rules['time']['minutes'];
			}
		}

		$processAt->setTime($hour, $minute, $second);

		return $processAt;
	}

	/**
	 * @param array $rules
	 *
	 * @return DateTime
	 * @throws Exception
	 */
	public function applyDelayWaitTimeMinuteRuleType(array $rules = []) : DateTime
	{
		// Sanity checks
		if (!isset($rules['amount'])) {
			throw new Exception("Bad amount is given");
		}

		// Create a new queue record with the updated process_at datetime
		try {
			$processAt = new DateTime();
			$processAt->add( new DateInterval( "PT{$rules['amount']}M" ) );
		} catch ( \Exception $e ) {
			throw new Exception($e->getMessage());
		}

		return $processAt;
	}

	/**
	 * @param array $rules
	 *
	 * @return DateTime
	 * @throws Exception
	 */
	public function applyDelayWaitTimeHourRuleType(array $rules = []) : DateTime
	{
		// Sanity checks
		if (!isset($rules['amount'])) {
			throw new Exception("Bad amount is given");
		}

		// Create a new queue record with the updated process_at datetime
		try {
			$processAt = new DateTime();
			$processAt->add( new DateInterval( "PT{$rules['amount']}H" ) );
		} catch ( \Exception $e ) {
			throw new Exception($e->getMessage());
		}

		return $processAt;
	}

	/**
	 * @param array $rules
	 *
	 * @return DateTime
	 * @throws Exception
	 */
	public function applyDelayWaitTimeWeekRuleType(array $rules = []) : DateTime
	{
		// Sanity checks
		if (!isset($rules['amount'])) {
			throw new Exception("Bad amount is given");
		}

		// Create a new queue record with the updated process_at datetime
		try {
			$processAt = new DateTime();
			$processAt->add( new DateInterval( "P{$rules['amount']}W" ) );
		} catch ( \Exception $e ) {
			throw new Exception($e->getMessage());
		}

		// Now add a new queue record
		return $processAt;
	}

	/**
	 * @param array $rules
	 *
	 * @return DateTime
	 * @throws Exception
	 */
	public function applyDelayWaitUntilHourRuleType(array $rules = []) : DateTime
	{
		// Sanity checks
		if (!isset($rules['time'])) {
			throw new Exception("Bad time is given");
		}

		if (!isset($rules['time']['hours'])) {
			throw new Exception("Bad time hours is given");
		}

		if (!isset($rules['time']['minutes'])) {
			throw new Exception("Bad time minutes is given");
		}

		// Consider offset
		//$this->considerOffset($rules);

		$processAt = new DateTime();
		$processAt->setTime($rules['time']['hours'], $rules['time']['minutes'], 0);

		$now = new DateTime();
		if($processAt < $now) {
			// date is in the past - set to next day
			$processAt->modify('+1 day');
		}

		return $processAt;
	}

	/**
	 * @param array $rules
	 * @param array $dateArray
	 *
	 * @return DateTime
	 * @throws Exception
	 */
	public function applyDelayWaitUntilDateRuleType(array $rules = [], array $dateArray) : DateTime
	{
		if (!isset($dateArray['day']) && !isset($dateArray['month']) && !isset($dateArray['year'])) {
			// If the personal field value is not set, we can't proceed. Let's keep the row untouched.
			throw new Exception(__METHOD__, "no date array values");
		}

		// Get the element date relation and number of days
		$relation = $rules['relation'];
		$days = $rules['days'];

		// Once you have the specific date, use the 'days' and 'relation' from the workflow element and update the queue
		$personalTimestamp = mktime(null, null, null, $dateArray['month'], $dateArray['day'], $dateArray['year']);
		return $this->compareDates($personalTimestamp, $relation, $days);
	}

	/**
	 * @param int $personalTimestamp
	 * @param int $relation
	 * @param int $days
	 *
	 * @return DateTime
	 * @throws Exception
	 */
	private function compareDates($personalTimestamp, $relation, $days): DateTime
	{
		$pfDateTime = new DateTime();
		$pfDateTime->setTimestamp($personalTimestamp);
		$processAt = clone $pfDateTime;

		// If the relation is 'after' a PF date (like send x days after PF date) so we just need to check if the PF date arrived, and if so, to update the queue table with process_at x days ahead
		// If the relation is 'before' a PF date (like send x days BEFORE PF date) so we need to check if the PF date is x days from now, if so, to update the queue table with process_at NOW()

		if ($relation == 2) {

			// BEFORE
			$toShift = new DateInterval("P{$days}D");

			// Update process_at
			$processAt->sub($toShift);
		}
		elseif ($relation == 1) {

			// AFTER
			try {
				$toShift = new DateInterval("P{$days}D");
			} catch (\Exception $e) {
				throw new Exception('Could not get interval to add', 400);
			}

			// Update process_at
			$processAt->add($toShift);
		}

		// Todo: implement and consider 'repeatable every year'

		return $processAt;
	}
}