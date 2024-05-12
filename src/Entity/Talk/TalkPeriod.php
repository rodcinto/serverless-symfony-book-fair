<?php

namespace App\Entity\Talk;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\SelfValidateTrait;

class TalkPeriod
{
  use SelfValidateTrait;

  #[Assert\NotNull]
  #[Assert\Type('\DateTimeInterface')]
  #[Assert\LessThan(propertyPath: "end", message: "The begin time must be earlier than the end time.")]
  public ?\DateTimeInterface $begin = null;

  #[Assert\NotNull]
  #[Assert\Type('\DateTimeInterface')]
  #[Assert\GreaterThan(propertyPath: "begin", message: "The end time must be later than the begin time.")]
  public ?\DateTimeInterface $end = null;

  public function __construct(
    ?\DateTimeInterface $begin = null,
    ?\DateTimeInterface $end = null,
  ) {
    $this->begin = $begin;
    $this->end = $end;

    $this->validateSelf();
  }
}
