<?php

namespace App\Api;

use App\Api\DTO\EpisodeFilter;
use App\App\AddReview\Handler;
use App\App\AddReview\ReviewInput;
use App\Infrastructure\Persistence\EpisodeRepository;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EpisodeController extends AbstractController
{
    public function __construct(
        private Handler $addReviewHandler,
        private EpisodeRepository $episodeRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private PaginatorInterface $paginator,
    ) {
    }

    #[Route(path: '/api/v1/episodes/{episodeId}/review', requirements: ['episodeId' => '\d+'], methods: ['POST'])]
    public function addReview(int $episodeId, Request $request): Response
    {
        $episode = $this->episodeRepository->find($episodeId);
        if ($episode === null) {
            return $this->json([
                'error' => 'Episode not found',
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $input = $this->serializer->deserialize($request->getContent(), ReviewInput::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        $errors = $this->validator->validate($input);
        if ($errors->count() > 0) {
            return $this->json([
                'error' => $this->formatErrorDetails($errors),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->addReviewHandler->addReview($episode, $input);

        return $this->json('success', Response::HTTP_CREATED);
    }

    #[Route(path: '/api/v1/episodes', methods: ['GET'])]
    public function episodes(Request $request): Response
    {
        $dateFrom = $request->query->get('date_from');
        $dateTo = $request->query->get('date_to');
        $season = $request->query->get('season');

        try {
            $from = $dateFrom ? new \DateTimeImmutable($dateFrom) : null;
            $to = $dateTo ? new \DateTimeImmutable($dateTo) : null;
        } catch (\Exception) {
            return $this->json(['error' => 'Invalid query format'], Response::HTTP_BAD_REQUEST);
        }

        $filter = new EpisodeFilter(
            $season ? (int) $season : null,
            $from,
            $to,
        );
        $query = $this->episodeRepository->getQuery($filter);
        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10,
        );

        return $this->json([
            'info' => [
                'total' => $pagination->getTotalItemCount(),
                'page' => $pagination->getCurrentPageNumber(),
                'pages' => (int) ($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            ],
            'items' => $pagination->getItems(),
        ], Response::HTTP_CREATED, context: ['groups' => ['api']]);
    }

    private function formatErrorDetails(ConstraintViolationListInterface $errors): array
    {
        $errorDetails = [];
        foreach ($errors as $error) {
            $propertyPath = $error->getPropertyPath();
            $errorDetails[$propertyPath][] = $error->getMessage();
        }

        return $errorDetails;
    }
}
