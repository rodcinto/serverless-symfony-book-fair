<?php

namespace App\Tests\Unit\Service;

use App\Service\ContextParser;
use PHPUnit\Framework\TestCase;

class ContextParserTest extends TestCase
{
  public function testCreateFromLambdaArray(): void
  {
    $context = ContextParser::fromLambdaContext($this->sampleArray());
    $this->assertEquals('3054e733-6c68-4fe5-b038-53953299d5ba', $context->cognitoUsername);
    $this->assertEquals(['organizers'], $context->cognitoRoles);
  }

  /**
   * @return string[]
   */
  private function sampleArray(): array
  {
    return [
      "accountId" => "643901222908",
      "apiId" => "f6jj0kjh4c",
      "authorizer" => [
        "jwt" => [
          "claims" => [
            "at_hash" => "RSI6VZXBXcwz3lSU0_JKZg",
            "aud" => "2r04jer0vr36pm4jgpjlm4j7au",
            "auth_time" => "1715374596",
            "cognito:groups" => "[organizers]",
            "cognito:username" => "3054e733-6c68-4fe5-b038-53953299d5ba",
            "email" => "org.johnson@rods-book.fair",
            "email_verified" => "true",
            "event_id" => "6c1c7e63-c72f-410a-9e5a-dde8a9ef5d3e",
            "exp" => "1715378196",
            "family_name" => "Johnson",
            "gender" => "Male",
            "given_name" => "Orgzer",
            "iat" => "1715374596",
            "iss" => "https://cognito-idp.eu-central-1.amazonaws.com/eu-central-1_Tbxz7w16V",
            "jti" => "3a2b227a-398f-4f43-a7bd-abceb21e1225",
            "sub" => "3054e733-6c68-4fe5-b038-53953299d5ba",
            "token_use" => "id"
          ],
          "scopes" => null
        ]
      ],
      "domainName" => "f6jj0kjh4c.execute-api.eu-central-1.amazonaws.com",
      "domainPrefix" => "f6jj0kjh4c",
      "http" => [
        "method" => "POST",
        "path" => "/talk",
        "protocol" => "HTTP/1.1",
        "sourceIp" => "95.90.113.169",
        "userAgent" => "PostmanRuntime/7.38.0"
      ],
      "requestId" => "Xkq1Jh8IFiAEMzw=",
      "routeKey" => "ANY /{proxy+}",
      "time" => "10/May/2024:21:02:11 +0000",
      "timeEpoch" => 1715374931881
    ];
  }
}
