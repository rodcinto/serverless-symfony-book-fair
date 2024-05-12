<?php

namespace App\Message;

use App\Dto\TalkInputDto;

final class CreateTalkMessage
{
  public function __construct(
    private TalkInputDto $content,
  ) {
  }

  public function getContent(): string
  {
    return $this->content->toJson();
  }
}
