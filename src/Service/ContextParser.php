<?php

namespace App\Service;

use Aws\Result as AWSResult;

class ContextParser
{
  public function __construct(
    public readonly string $cognitoUsername,
    public readonly ?string $cognitoEmail,
    /** @var array<string> */
    public readonly ?array $cognitoRoles = []
  ) {
  }

  /**
   * @param array<mixed> $context
   */
  public static function fromLambdaContext(array $context): ?self
  {
    if (
      isset($context['authorizer']) &&
      isset($context['authorizer']['jwt']) &&
      isset($context['authorizer']['jwt']['claims'])
    ) {
      $claims = $context['authorizer']['jwt']['claims'];

      if (!isset($claims['cognito:username'])) {
        return null;
      }

      $cognitoGroups = [];
      if (isset($claims['cognito:groups'])) {
        $cognitoGroups = str_replace(['[', ']'], '', $claims['cognito:groups']);

        $cognitoGroups = explode(',', $cognitoGroups);
      }

      return new self($claims['cognito:username'], $claims['email'], $cognitoGroups);
    }

    return null;
  }

  /**
   * @param AWSResult<mixed> $result
   */
  public static function fromCognitoApi(AWSResult $result): ?self
  {
    $findAttribute = function (string $name) use ($result) {
      foreach ($result->get('UserAttributes') as $attrItem) {
        if ($attrItem['Name'] === $name) {
          return $attrItem['Value'];
        }
      }
    };

    return new self($result->get('Username'), $findAttribute('email'), [$findAttribute('custom:role')]);
  }
}
