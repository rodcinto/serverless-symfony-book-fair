<?php

namespace App\Tests\Unit\Entity\Member;

use App\Entity\Member\User;
use App\Tests\Persona\Authorino;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
  public function testTwoUsersAreSame(): void
  {
    $authorino = new Authorino();
    $user1 = new User($authorino->id, $authorino->email, $authorino->role);
    $user2 = new User($authorino->id, $authorino->email, $authorino->role);

    $this->assertTrue($user1->isSame($user2));
  }
}
