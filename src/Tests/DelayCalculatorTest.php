<?php

namespace DelayCalculator\Tests;

use DelayCalculator\DelayCalculator;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class DelayCalculatorTest
 * @package DelayCalculator
 */
class DelayCalculatorTest extends TestCase {

	public function testApplyDelayWaitTimeDayRuleType() {
		$dc = new DelayCalculator();

		// Current date
		$rules = [
			'amount' => 0
		];
		$actualRes = $dc->applyDelayWaitTimeDayRuleType($rules);
		$expectedRes = new \DateTime();
		$expectedRes->setTime(0,1,0);
		$this->assertIsObject($actualRes);
		$this->assertEquals($expectedRes->getTimestamp(), $actualRes->getTimestamp());

		// Upcoming day
		$shiftDays = 5;
		$rules = [
			'amount' => $shiftDays
		];
		$actualRes = $dc->applyDelayWaitTimeDayRuleType($rules);
		$expectedRes = new \DateTime();
		$expectedRes->add(new \DateInterval("P{$shiftDays}D"));
		$expectedRes->setTime(0,1,0);
		$this->assertIsObject($actualRes);
		$this->assertEquals($expectedRes->getTimestamp(), $actualRes->getTimestamp());

		// Using past interval
		$rules = [
			'amount' => 50
		];
		$actualRes = $dc->applyDelayWaitTimeDayRuleType($rules, "P30D");
		$expectedRes = new \DateTime();
		$expectedRes->add(new \DateInterval("P20D"));
		$expectedRes->setTime(0,1,0);
		$this->assertIsObject($actualRes, "process_at must be a DateTime object");
		$this->assertEquals($expectedRes->getTimestamp(), $actualRes->getTimestamp(), "Delays are not equal");

		// Expect exception upon empty input
		$this->expectException(Exception::class);
		$rules = [];
		$actualRes = $dc->applyDelayWaitTimeDayRuleType($rules);
	}

	public function testApplyDelayWaitTimeMinuteRuleType() {

	}

	public function testApplyDelayWaitUntilHourRuleType() {

	}

	public function testApplyDelayWaitTimeWeekRuleType() {

	}

	public function testApplyDelayWaitUntilDateRuleType() {

	}

	public function testApplyDelayWaitTimeHourRuleType() {

	}
}
