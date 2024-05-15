<?php

namespace App\Controller;

use App\Dto\SubscribeToTalkDto;
use App\Dto\TalkInputDto;
use App\Dto\TalkPrepareDto;
use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use App\Entity\Member\User;
use App\Entity\Talk\Talk;
use App\Factory\TalkFactory;
use App\Infrastructure\DynamoDBAdapter;
use App\Message\SubscriptionMessage;
use App\Security\TalkVoter;
use App\Service\CreateTalk;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV1;
use Symfony\Component\Workflow\WorkflowInterface;
use Aws\Result;
use Symfony\Component\Messenger\MessageBusInterface;

class TalkController extends AbstractController
{
  public function __construct(private DynamoDBAdapter $dynamoDBAdapter)
  {
  }

  #[Route('/talk', methods: [Request::METHOD_POST], name: 'app_talk_create')]
  public function create(
    Security $security,
    Request $request,
    CreateTalk $createTalkService
  ): JsonResponse {
    $response = [];

    $payload = $request->getPayload();
    try {
      $author = new Author($payload->get('authorId'), $payload->get('authorEmail'));
    } catch (\Throwable $th) {
      $response['error'][] = 'Please provide a valid existing `authorId` and `authorEmail`';
      $response['error'][] = $th->getMessage();
      return $this->json($response, 400);
    }

    /** @var User|null */
    $user = $security->getUser();
    if (!$user) {
      $response['error'][] = 'Organizer not found.';
      return $this->json($response, 400);
    }

    $organizer = new Organizer($user->getUserIdentifier(), $user->email);

    try {
      $parsedBegin = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $payload->get('begin'));
      $parsedEnd = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $payload->get('end'));
    } catch (\Throwable $th) {
      $response['error'][] = $th->getMessage();
      return $this->json($response, 400);
    }

    $talkInputDto = new TalkInputDto(
      $organizer,
      $author,
      $payload->get('title'),
      $parsedBegin,
      $parsedEnd
    );

    try {
      $response['talk'] = $createTalkService->create($talkInputDto);
    } catch (\Throwable $th) {
      $response['error'] = $th->getMessage();
      $response['error_stack'] = $th->getTraceAsString();
      return $this->json($response, 400);
    }

    return $this->json($response, 201);
  }

  #[Route('/talk/{id}', methods: [Request::METHOD_PATCH], name: 'app_talk_prepare')]
  public function prepare(
    UuidV1 $id,
    Request $request,
    WorkflowInterface $talkStateMachine
  ): JsonResponse {
    /** @var Result<mixed>|null */
    $dynamoResult = $this->dynamoDBAdapter->findById($id);
    if (!$dynamoResult) {
      return $this->json([
        'error' => sprintf('Talk id "%s" not found.', (string)$id)
      ], 400);
    }

    $talk = TalkFactory::fromDynamoDB($dynamoResult);

    $this->denyAccessUnlessGranted(TalkVoter::EDIT, $talk);

    $payload = $request->getPayload();

    $prepareDto = new TalkPrepareDto(
      $payload->get('title'),
      $payload->get('teaser'),
      $payload->get('description')
    );

    $talk->prepare($prepareDto);

    $talkStateMachine->apply($talk, Talk::TRANSITION_TO_PUBLISHED);

    $this->dynamoDBAdapter->putItem($talk->toArray());

    return $this->json($talk->toArray(), 200);
  }

  #[Route('/talk/{id}/start', methods: [Request::METHOD_POST], name: 'app_talk_start')]
  public function start(
    UuidV1 $id,
    WorkflowInterface $talkStateMachine
  ): JsonResponse {
    /** @var Result<mixed>|null */
    $dynamoResult = $this->dynamoDBAdapter->findById($id);
    if (!$dynamoResult) {
      return $this->json([
        'error' => sprintf('Talk id "%s" not found.', (string)$id)
      ], 400);
    }

    $talk = TalkFactory::fromDynamoDB($dynamoResult);

    $this->denyAccessUnlessGranted(TalkVoter::START, $talk);

    if ($talk->getCurrentPlace() === Talk::STATE_STARTED) {
      return $this->json([
        'message' => 'started'
      ], 200);
    }

    try {
      $talkStateMachine->apply($talk, Talk::TRANSITION_TO_STARTED);
      $this->dynamoDBAdapter->putItem($talk->toArray());
    } catch (\Throwable $th) {
      return $this->json([
        'error' => 'Could not start. Check if the Talk is published or isn\'t finished.'
      ], 400);
    }

    return $this->json([
      'message' => 'started'
    ], 200);
  }

  #[Route('/talk/{id}/finish', methods: [Request::METHOD_POST], name: 'app_talk_finish')]
  public function finish(
    UuidV1 $id,
    WorkflowInterface $talkStateMachine
  ): JsonResponse {
    /** @var Result<mixed>|null */
    $dynamoResult = $this->dynamoDBAdapter->findById($id);
    if (!$dynamoResult) {
      return $this->json([
        'error' => sprintf('Talk id "%s" not found.', (string)$id)
      ], 400);
    }

    $talk = TalkFactory::fromDynamoDB($dynamoResult);

    $this->denyAccessUnlessGranted(TalkVoter::FINISH, $talk);

    if ($talk->getCurrentPlace() === Talk::STATE_FINISHED) {
      return $this->json([
        'message' => 'finished'
      ], 200);
    }

    try {
      $talkStateMachine->apply($talk, Talk::TRANSITION_TO_FINISHED);
      $this->dynamoDBAdapter->putItem($talk->toArray());
    } catch (\Throwable $th) {
      return $this->json([
        'error' => 'Could not finish. Make sure the Talk has been started.'
      ], 400);
    }

    return $this->json([
      'message' => 'finished'
    ], 200);
  }

  #[Route('/talk/{id}/subscribe', methods: [Request::METHOD_POST], name: 'app_talk_subscribe')]
  public function subscribe(
    UuidV1 $id,
    Security $security,
    MessageBusInterface $bus
  ): JsonResponse {
    /** @var Result<mixed>|null */
    $dynamoResult = $this->dynamoDBAdapter->findById($id);
    if (!$dynamoResult) {
      return $this->json([
        'error' => sprintf('Talk id "%s" not found.', (string)$id)
      ], 400);
    }

    /** @var User|null */
    $user = $security->getUser();
    if (!$user) {
      $response['error'][] = 'Organizer not found.';
      return $this->json($response, 400);
    }

    $talk = TalkFactory::fromDynamoDB($dynamoResult);
    if (!$talk->canSubscribe()) {
      return $this->json([
        'error' => sprintf('Could not subscribe to talk "%s". Check if the talk is published or hasn\'t already started.', $id)
      ], 400);
    }

    $dto = new SubscribeToTalkDto(
      (string) $id,
      $user->getUserIdentifier(),
      $user->email
    );

    $bus->dispatch(new SubscriptionMessage($dto));

    return $this->json([
      'message' => 'Subscribed',
      'talkId' => $id,
      'userId' => $user->getUserIdentifier(),
    ], 201);
  }
}
