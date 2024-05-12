<?php

namespace App\Message;

use Psr\Log\LoggerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateTalkHandler
{
  private $logger;

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public function __invoke(CreateTalkMessage $message)
  {
    $this->logger->alert('CREATE TALK RECEIVED DATA: ' . $message->getContent());
  }
}
