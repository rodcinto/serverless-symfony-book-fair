<?php

namespace App\Security;

use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use App\Entity\Member\User;
use App\Entity\Talk\Talk;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TalkVoter extends Voter
{
  // these strings are just invented: you can use anything
  public const EDIT = 'edit';

  protected function supports(string $attribute, mixed $subject): bool
  {
    // if the attribute isn't one we support, return false
    if (!in_array($attribute, [self::EDIT])) {
      return false;
    }

    // only vote on `Talk` objects
    if (!$subject instanceof Talk) {
      return false;
    }

    return true;
  }

  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
  {
    $user = $token->getUser();

    if (!$user instanceof User) {
      // the user must be logged in; if not, deny access
      return false;
    }

    // you know $subject is a Talk object, thanks to `supports()`
    /** @var Talk $talk */
    $talk = $subject;

    return match ($attribute) {
      self::EDIT => $this->canEdit($talk, $user),
      default => throw new \LogicException('This code should not be reached!')
    };
  }

  private function canEdit(Talk $talk, User $user): bool
  {
    if ($user instanceof Author) {
      return $talk->isAssignedTo($user);
    }
    if ($user instanceof Organizer) {
      return $talk->hasBeenCreatedBy($user);
    }

    return false;
  }
}
