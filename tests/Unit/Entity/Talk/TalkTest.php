<?php

namespace App\Tests\Unit\Entity\Member;

use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use App\Entity\Talk\Talk;
use App\Tests\Persona\Authorino;
use App\Tests\Persona\Orgzer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class TalkTest extends TestCase
{
  public function testCreateATalk(): void
  {
    $talkData = $this->validTalkData();
    $talk = new Talk(
      $talkData['id'],
      $talkData['title'],
      $talkData['organizer'],
      $talkData['author'],
      $talkData['begin'],
      $talkData['end']
    );
    $this->assertNotEmpty($talk->toArray());
  }

  public function testTitleWrongLength()
  {
    // $this->expectException(RuntimeException::class);
    // $this->expectExceptionMessage('too long');

    $talkData = $this->validTalkData();
    $createTalk = fn ($len) => new Talk(
      $talkData['id'],
      $this->generateRandomString($len),
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

  private function validTalkData(): array
  {
    $currentTime = new \DateTimeImmutable();
    $twoHoursLater = $currentTime->modify('+2 hours');

    $orgzer = new Orgzer();
    $organizer = new Organizer($orgzer->id, $orgzer->email, $orgzer->role);

    $authorino = new Authorino();
    $author = new Author($authorino->id, $authorino->email, $authorino->role);

    echo 'Now: ' . $currentTime->format('c');
    echo 'Then: ' . $twoHoursLater->format('c');

    return [
      'id' => Uuid::v1(),
      'title' => 'How to make a nice Book Fair',
      'organizer' => $organizer,
      'author' => $author,
      'begin' => $currentTime,
      'end' => $twoHoursLater,
    ];
  }

  private function generateRandomString($length = 10)
  {
    $stringRange = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(
      str_shuffle(
        str_repeat($stringRange, ceil($length / strlen($stringRange)))
      ),
      1,
      $length
    );
  }
}
