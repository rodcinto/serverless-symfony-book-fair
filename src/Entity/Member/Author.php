<?php

namespace App\Entity\Member;

class Author extends User
{
  public function __construct(
    string $id,
    string $email,
  ) {
    parent::__construct($id, $email, [Role::AUTHOR]);
    //TODO: Verify Author data in Cognito.
  }
}
