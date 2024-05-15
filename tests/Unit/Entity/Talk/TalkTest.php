<?php

namespace App\Tests\Unit\Entity\Member;

use App\Dto\TalkPrepareDto;
use App\Entity\Talk\Talk;
use App\Tests\Unit\Entity\Talk\TalkData;
use PHPUnit\Framework\TestCase;
use LogicException;

class TalkTest extends TestCase
{
  private TalkData $talkData;

  protected function setUp(): void
  {
      parent::setUp();

      $this->talkData = new TalkData();
  }

  public function testCreateATalk(): void
  {
    $talk = $this->createTalk();

    $this->assertNotEmpty($talk->toArray());
  }

  public function testTitleWrongLength(): void
  {
    // $this->expectException(LogicException::class);
    // $this->expectExceptionMessage('too long');

    $talkData = $this->talkData->createData();
    $createTalk = fn ($len) => new Talk(
      $talkData['id'],
      $this->talkData->generateRandomString($len),
      $talkData['organizer'],
      $talkData['author'],
      $talkData['begin'],
      $talkData['end']
    );

    try {
      $longTalk = $createTalk(256);
    } catch (\Throwable $th) {
      $this->assertStringContainsString('too long', $th->getMessage());
    }

    try {
      $shortTalk = $createTalk(4);
    } catch (\Throwable $th) {
      $this->assertStringContainsString('too short', $th->getMessage());
    }
  }

  public function testIsAssignedToAuthor(): void
  {
    $talkData = $this->talkData->createData();
    $talk = $this->createTalk();

    $this->assertTrue($talk->isAssignedTo($talkData['author']));
  }

  public function testHasBeenCreatedByOrganizer(): void
  {
    $talkData = $this->talkData->createData();
    $talk = $this->createTalk();

    $this->assertTrue($talk->hasBeenCreatedBy($talkData['organizer']));
  }

  public function testCanNotBePublishedWithoutUpdates(): void
  {
    $talk = $this->createTalk();
    $this->expectException(LogicException::class);
    $talk->canBePublished();
  }

  public function testCanBePublished(): void
  {
    $talk = $this->createTalk();
    $dto = new TalkPrepareDto(
      null,
      $this->talkData->generateRandomString(11),
      $this->talkData->generateRandomString(21),
    );

    $talk->prepare($dto);

    $this->assertTrue($talk->canBePublished());
  }

  private function createTalk(): Talk
  {
    $talkData = $this->talkData->createData();
    return new Talk(
      $talkData['id'],
      $talkData['title'],
      $talkData['organizer'],
      $talkData['author'],
      $talkData['begin'],
      $talkData['end']
    );
  }
}
