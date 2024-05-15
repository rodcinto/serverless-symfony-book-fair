<?php

namespace App\Tests\Unit\Entity\Talk;

use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use App\Tests\Persona\Authorino;
use App\Tests\Persona\Orgzer;
use Symfony\Component\Uid\Uuid;

class TalkData
{
  /**
   * @return array<mixed>
   */
  public function createData(): array
  {
    $currentTime = new \DateTimeImmutable();
    $twoHoursLater = $currentTime->modify('+2 hours');

    $orgzer = new Orgzer();
    $organizer = new Organizer($orgzer->id, $orgzer->email);

    $authorino = new Authorino();
    $author = new Author($authorino->id, $authorino->email);

    return [
      'id' => Uuid::v1(),
      'title' => 'How to make a nice Book Fair',
      'organizer' => $organizer,
      'author' => $author,
      'begin' => $currentTime,
      'end' => $twoHoursLater,
    ];
  }

  public function generateRandomString(int $length = 10): string
  {
    $stringRange = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(
      str_shuffle(
        str_repeat($stringRange, (int)ceil($length / strlen($stringRange)))
      ),
      1,
      $length
    );
  }
}
