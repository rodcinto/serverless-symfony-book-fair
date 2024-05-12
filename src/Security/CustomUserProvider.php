<?php

namespace App\Security;

use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use App\Entity\Member\Role;
use App\Entity\Member\User;
use App\Service\ContextParser;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CustomUserProvider implements UserProviderInterface
{
  public function __construct(
    private CognitoIdentityProviderClient $cognitoClient
  ) {
  }

  public function loadUserByIdentifier(string $identifier): UserInterface
  {
    try {
      $user = $this->cognitoClient->getUser([
        'AccessToken' => $identifier
      ]);
    } catch(CognitoIdentityProviderException $exception) {
      // Log that thing.
      throw new BadCredentialsException('Expired token or somehting 🤡');
    }

    $parsedUser = ContextParser::fromCognitoApi($user);

    switch ($parsedUser->cognitoRoles[0]) {
      case Role::ORGANIZER->value:
        return new Organizer($parsedUser->cognitoUsername, $parsedUser->cognitoEmail);
      case Role::AUTHOR->value:
        return new Author($parsedUser->cognitoUsername, $parsedUser->cognitoEmail);
      default:
      case Role::GUEST->value:
        return new User(
          $parsedUser->cognitoUsername,
          $parsedUser->cognitoEmail,
          $parsedUser->cognitoRoles
        );
    }
  }

  public function refreshUser(UserInterface $user): UserInterface
  {
    return $user;
  }

  public function supportsClass($class): bool
  {
    return User::class === $class;
  }
}
