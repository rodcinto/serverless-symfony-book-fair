<?php

namespace App\Dto;

class TalkPrepareDto
{
  public function __construct(
    public readonly ?string $title,
    public readonly string $teaser,
    public readonly string $description,
  ) {
  }
}
