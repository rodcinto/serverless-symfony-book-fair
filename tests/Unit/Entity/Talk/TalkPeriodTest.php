<?php

namespace App\Tests\Unit\Entity\Member;

use App\Entity\Talk\TalkPeriod;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TalkPeriodTest extends TestCase
{
  public function testCanCreatePeriod()
  {
    $currentTime = new \DateTimeImmutable();
    $twoHoursLater = $currentTime->modify('+2 hours');

    $tp = new TalkPeriod($currentTime, $twoHoursLater);

    $this->assertInstanceOf(\DateTimeImmutable::class, $tp->begin);
    $this->assertInstanceOf(\DateTimeImmutable::class, $tp->end);
  }

  public function testBeginMustBeEarlierThanEnd()
  {
    $this->expectException(RuntimeException::class);

    $currentTime = new \DateTimeImmutable();
    $twoHoursLater = $currentTime->modify('-2 hours');

    $tp = new TalkPeriod($currentTime, $twoHoursLater);
  }
}
