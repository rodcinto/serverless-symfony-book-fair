<?php

namespace App\Entity\Talk;

use App\Dto\TalkPrepareDto;
use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use App\Entity\SelfValidateTrait;
use LogicException;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class Talk
{
  use SelfValidateTrait;

  public const STATE_DRAFT = 'draft';
  public const STATE_PUBLISHED = 'published';
  public const STATE_STARTED = 'started';
  public const STATE_FINISHED = 'finished';
  public const TRANSITION_TO_PUBLISHED = 'to_published';
  public const TRANSITION_TO_STARTED = 'to_started';
  public const TRANSITION_TO_FINISHED = 'to_finished';

  private Duration $duration;
  private string $currentPlace;

  // Published properties:
  #[Assert\Length(min: 10, max: 200, groups: ['publication'])]
  private string $teaser = '';
  #[Assert\Length(min: 20, max: 3000, groups: ['publication'])]
  private string $description = '';

  public function __construct(
    #[Assert\Uuid(groups: ['creation'])]
    private string $id,
    #[Assert\Length(min: 5, max: 255, groups: ['creation'])]
    private string $title,
    private Organizer $organizer,
    private Author $author,
    #[Assert\Type('\DateTimeInterface', groups: ['creation'])]
    private \DateTimeInterface $begin,
    #[Assert\Type('\DateTimeInterface', groups: ['creation'])]
    private \DateTimeInterface $end,
  ) {
    $this->duration = new Duration($begin, $end);

    $this->validateSelf(['creation']);
  }

  public function prepare(TalkPrepareDto $dto): void
  {
    if (isset($dto->title)) {
      $this->title = $dto->title;
    }
    $this->teaser = $dto->teaser;
    $this->description = $dto->description;
  }

  public function toArray(): array
  {
    $data = [
      'id' => $this->id,
      'title' => $this->title,
      'organizerId' => $this->organizer->id,
      'authorId' => $this->author->id,
      'begin' =>
      $this->duration->begin ? $this->begin->format('c') : null,
      'end' =>
      $this->duration->end ? $this->duration->end->format('c') : null,
      'currentPlace' => $this->currentPlace ?? self::STATE_DRAFT,
    ];

    if ($this->teaser !== '') {
      $data['teaser'] = $this->teaser;
    }
    if ($this->description !== '') {
      $data['description'] = $this->description;
    }

    return $data;
  }

  public function isAssignedTo(Author $givenAuthor): bool
  {
    return $this->author->isSame($givenAuthor);
  }

  public function hasBeenCreatedBy(Organizer $givenOrganizer): bool
  {
    return $this->organizer->isSame($givenOrganizer);
  }

  public function getCurrentPlace(): string
  {
    return $this->currentPlace;
  }

  public function setCurrentPlace(string $currentPlace, array $context = []): void
  {
    $this->currentPlace = $currentPlace;
  }

  public function canBePublished(): bool
  {
    $this->validateSelf(['creation', 'publication']);

    return count($this->errors) === 0;
  }
}
