<?php
namespace App\EventSubscriber;

use App\Entity\Talk\Talk;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class TalkPublishSubscriber implements EventSubscriberInterface
{
  public function guardPublish(GuardEvent $event): void
  {
    /** @var Talk $talk */
    $talk = $event->getSubject();

    if (!$talk->canBePublished()) {
      $event->setBlocked(true, 'Talk can not be published.');
    }
  }

  public static function getSubscribedEvents(): array
  {
    return [
      'workflow.talk.guard.to_published' => ['guardPublish'],
    ];
  }
}
