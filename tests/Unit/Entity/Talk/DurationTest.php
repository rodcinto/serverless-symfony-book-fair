<?php

namespace App\Tests\Unit\Entity\Member;

use App\Entity\Talk\Duration;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DurationTest extends TestCase
{
  public function testCanCreatePeriod()
  {
    $currentTime = new \DateTimeImmutable();
    $twoHoursLater = $currentTime->modify('+2 hours');

    $tp = new Duration($currentTime, $twoHoursLater);

    $this->assertInstanceOf(\DateTimeImmutable::class, $tp->begin);
    $this->assertInstanceOf(\DateTimeImmutable::class, $tp->end);
  }

  public function testBeginMustBeEarlierThanEnd()
  {
    $this->expectException(RuntimeException::class);

    $currentTime = new \DateTimeImmutable();
    $twoHoursLater = $currentTime->modify('-2 hours');

    $tp = new Duration($currentTime, $twoHoursLater);
  }
}
