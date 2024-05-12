<?php

namespace App\Entity\Talk;

use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\SelfValidateTrait;

class Talk
{
  use SelfValidateTrait;

  private Duration $duration;

  public function __construct(
    #[Assert\Uuid()]
    private string $id,
    #[Assert\Length(min: 5, max: 255)]
    private string $title,
    private Organizer $organizer,
    private Author $author,
    #[Assert\Type('\DateTimeInterface')]
    private \DateTimeInterface $begin,
    #[Assert\Type('\DateTimeInterface')]
    private \DateTimeInterface $end,
  ) {
    $this->duration = new Duration($begin, $end);

    $this->validateSelf();
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'organizerId' => $this->organizer->id,
      'authorId' => $this->author->id,
      'begin' =>
        $this->duration->begin ? $this->begin->format('c') : null,
      'end' =>
        $this->duration->end ? $this->duration->end->format('c') : null,
    ];
  }
}
