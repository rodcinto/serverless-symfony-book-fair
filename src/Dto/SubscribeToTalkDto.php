<?php
namespace App\Dto;

use Symfony\Component\Uid\UuidV1;

class SubscribeToTalkDto
{
  public function __construct(
    public readonly string $talkId,
    public readonly string $userId,
    public readonly string $email
  ) {
  }
}
