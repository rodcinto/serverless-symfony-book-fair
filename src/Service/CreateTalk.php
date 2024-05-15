<?php

namespace App\Service;

use Aws\DynamoDb\DynamoDbClient;
use App\Dto\TalkInputDto;
use App\Entity\Talk\Talk;
use App\Infrastructure\DynamoDBAdapter;
use Aws\DynamoDb\Marshaler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\Attribute\Required;

class CreateTalk
{
  public function __construct(
    private DynamoDBAdapter $dynamoDBAdapter,
    private LoggerInterface $logger
  ) {
  }

  /**
   * @return array<mixed>
   */
  public function create(TalkInputDto $dto): array
  {
    $talk = new Talk(
      Uuid::v1(),
      $dto->title,
      $dto->organizer,
      $dto->author,
      $dto->begin,
      $dto->end
    );

    try {
      $this->dynamoDBAdapter->putItem($talk->toArray());
    } catch (\Throwable $th) {
      $this->logger->error('Could not save to DynamoDB', [$th]);

      throw $th;
    }

    return $talk->toArray();
  }
}
