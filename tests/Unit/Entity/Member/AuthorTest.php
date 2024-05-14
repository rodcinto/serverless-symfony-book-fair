<?php

namespace App\Tests\Unit\Entity\Member;

use App\Entity\Member\User;
use App\Entity\Member\Author;
use App\Tests\Persona\Authorino;
use PHPUnit\Framework\TestCase;
use LogicException;

class AuthorTest extends TestCase
{
  private Authorino $authorino;

  protected function setUp(): void
  {
    parent::setUp();

    $this->authorino = new Authorino();
  }

  public function testCanCreateAuthor()
  {
    $author = new Author($this->authorino->id, $this->authorino->email, $this->authorino->role);

    $this->assertInstanceOf(User::class, $author);
  }

  public function testCanNotCreateAuthorWithInvalidUuid()
  {
    $this->expectException(LogicException::class);
    $this->expectExceptionMessage('not a valid UUID');

    $author = new Author('12345', $this->authorino->email, $this->authorino->role);
  }
}
