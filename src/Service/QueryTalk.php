<?php

namespace App\Service;

use App\Entity\Talk\Talk;
use App\Factory\TalkFactory;
use Aws\DynamoDb\DynamoDbClient;
use Symfony\Component\Uid\UuidV1;

class QueryTalk
{
  public function __construct(private DynamoDbClient $dynamoDbClient)
  {
  }

  public function findById(UuidV1 $id): ?Talk
  {
    $result = $this->dynamoDbClient->getItem([
      'Key' => [
        'id' => [
          'S' => $id,
        ]
      ],
      'TableName' => 'Talks',
    ]);
    if (!$result) {
      return null;
    }
    return TalkFactory::fromDynamoDB($result);
  }
}
