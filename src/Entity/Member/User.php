<?php

namespace App\Entity\Member;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\SelfValidateTrait;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
  use SelfValidateTrait;

  public function __construct(
    #[Assert\Uuid()]
    public readonly string $id,
    public readonly string $email,
    public readonly array $roles
  ) {
    $this->validateSelf();
  }

  public function getRoles(): array
  {
    return $this->roles;
  }

  /**
   * Removes sensitive data from the user.
   *
   * This is important if, at any given point, sensitive information like
   * the plain-text password is stored on this object.
   */
  public function eraseCredentials(): void
  {
  }

  /**
   * Returns the identifier for this user (e.g. username or email address).
   */
  public function getUserIdentifier(): string
  {
    return $this->id;
  }
}
