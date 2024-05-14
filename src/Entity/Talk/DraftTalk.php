<?php

namespace App\Entity\Talk;

class DraftTalk extends Talk
{
  private Status $status = Status::DRAFT;

  public function toArray(): array
  {
    $parent = parent::toArray();
    return [
      ...$parent,
      'status' => $this->status
    ];
  }
}
