<?php

namespace App\Tests\Unit\Entity\Member;

use App\Entity\Member\User;
use App\Entity\Member\Organizer;
use App\Tests\Persona\Orgzer;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class OrganizerTest extends TestCase
{
  private Orgzer $orgzer;

  protected function setUp(): void
  {
      parent::setUp();

      $this->orgzer = new Orgzer();
  }
  public function testCanCreateOrganizer()
  {
    $organizer = new Organizer($this->orgzer->id, $this->orgzer->email, $this->orgzer->role);

    $this->assertInstanceOf(User::class, $organizer);
  }

  public function testCanNotCreateOrganizerWithInvalidUuid()
  {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('not a valid UUID');

    $organizer = new Organizer('123456', $this->orgzer->email, $this->orgzer->role);
  }
}
