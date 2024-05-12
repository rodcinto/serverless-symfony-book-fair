<?php

namespace App\Controller;

use App\Dto\TalkInputDto;
use App\Entity\Member\Author;
use App\Entity\Member\Organizer;
use App\Security\TalkVoter;
use App\Service\CreateTalk;
use App\Service\QueryTalk;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV1;

class TalkController extends AbstractController
{
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
    $user = $security->getUser();
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
  public function prepare(UuidV1 $id, QueryTalk $queryTalkService): JsonResponse
  {
    $talk = $queryTalkService->findById($id);
    $this->denyAccessUnlessGranted(TalkVoter::EDIT, $talk);

    dump($talk);

    return $this->json('If you can see this, you are the Author assigned or the Organizer who created the talk.', 200);
  }
}
