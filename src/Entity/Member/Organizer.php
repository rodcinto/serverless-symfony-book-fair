<?php

namespace App\Entity\Member;

class Organizer extends User
{
  public function __construct(
    string $id,
    string $email,
  ) {
    parent::__construct($id, $email, [Role::ORGANIZER]);
    //No need to verify organizer from Cognito once it comes from the session. Right?
  }
}
