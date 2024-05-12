<?php

namespace App\Service;

use Aws\DynamoDb\DynamoDbClient;
use App\Dto\TalkInputDto;
use App\Entity\Talk\Talk;
use Aws\DynamoDb\Marshaler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class CreateTalk
{
  public function __construct(
    private DynamoDbClient $dynamoDbClient,
    private LoggerInterface $logger
  ) {
  }

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

    $marshaler = new Marshaler();// I should inject this.
    $item = $marshaler->marshalJson(json_encode($talk->toArray()));

    $tableName = $_ENV['DYNAMODB_TALKS_TABLE'] ?? 'Talks'; // I should inject this.

    $params = [
      'TableName' => $tableName,
      'Item' => $item,
    ];

    $this->logger->info('Saving into DynamoDB', [$params]);

    try {
      $this->dynamoDbClient->putItem($params);
    } catch (\Throwable $th) {
      $this->logger->error('Could not save to DynamoDB', [$th]);

      throw $th;
    }

    return $talk->toArray();
  }
}