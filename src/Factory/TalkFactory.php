<?php

namespace App\Factory;

use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use App\Entity\Talk\Talk;
use Aws\Result;
use DateTimeImmutable;
use DateTimeInterface;

class TalkFactory
{
  public static function fromDynamoDB(Result $data): ?Talk
  {
    $item = $data->get('Item');

    $organizer = new Organizer($item['organizerId']['S'], null);
    $author = new Author($item['authorId']['S'], null);
    $parsedBegin = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $item['begin']['S']);
    $parsedEnd = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $item['end']['S']);


    return new Talk(
      $item['id']['S'],
      $item['title']['S'],
      $organizer,
      $author,
      $parsedBegin,
      $parsedEnd
    );
  }
}
