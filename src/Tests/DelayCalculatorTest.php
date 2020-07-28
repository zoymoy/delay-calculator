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

		$rules = [
			'amount' => 0
		];
		$actualRes = $dc->applyDelayWaitTimeDayRuleType($rules);
		$expectedRes = new \DateTime();
		$expectedRes->setTime(0,1,0);
		$this->assertIsObject($actualRes);
		$this->assertEquals($expectedRes->getTimestamp(), $actualRes->getTimestamp());

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
