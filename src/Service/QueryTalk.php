<?php

namespace App\Service;

use App\Entity\Talk\Talk;
use App\Factory\TalkFactory;
use Aws\DynamoDb\DynamoDbClient;
use Symfony\Component\Uid\UuidV1;
use Symfony\Contracts\Service\Attribute\Required;

class QueryTalk
{
  private string $tableName;

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
      'TableName' => $this->tableName,
    ]);
    if (!$result) {
      return null;
    }
    return TalkFactory::fromDynamoDB($result);
  }

  #[Required]
  public function setTableName(string $tableName): void
  {
    $this->tableName = $tableName;
  }
}
