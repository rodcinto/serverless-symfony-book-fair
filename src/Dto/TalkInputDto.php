<?php

namespace App\Dto;

use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use DateTimeImmutable;

class TalkInputDto
{
  public function __construct(
    public readonly Organizer $organizer,
    public readonly Author $author,
    public readonly string $title,
    public readonly DateTimeImmutable $begin,
    public readonly DateTimeImmutable $end,
  ) {
  }
}
