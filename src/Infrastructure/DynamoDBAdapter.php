<?php

namespace App\Infrastructure;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Aws\Result;
use Symfony\Component\Uid\UuidV1;
use Symfony\Contracts\Service\Attribute\Required;

class DynamoDBAdapter
{
  private string $tableName;

  public function __construct(
    private DynamoDbClient $dynamoDbClient,
    private Marshaler $marshaler
  ) {
  }

  #[Required]
  public function setTableName(string $tableName): void
  {
    $this->tableName = $tableName;
  }

  public function putItem(array $item): void
  {
    $itemEncoded = $this->marshaler->marshalJson(json_encode($item));
    $params = [
      'TableName' => $this->tableName,
      'Item' => $itemEncoded,
    ];

    $this->dynamoDbClient->putItem($params);
  }

  public function findById(UuidV1 $id): Result
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

    return $result;
  }
}
