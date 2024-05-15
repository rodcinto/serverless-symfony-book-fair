<?php
namespace App\Message;

use App\Dto\SubscribeToTalkDto;

class SubscriptionMessage
{
  public function __construct(
    private SubscribeToTalkDto $content,
  ) {
  }

  public function getContent(): string
  {
    $contentData = [
      'talkId' => $this->content->talkId,
      'userId' => $this->content->userId,
      'email' => $this->content->email,
    ];

    return json_encode($contentData);
  }
}
