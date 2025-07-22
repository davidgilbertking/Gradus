<?php

namespace App\Controller;

use App\DTO\Deployment\Deployment as DeploymentDTO;
use App\Entity\Deployment;
use App\Repository\DeploymentRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/deployment/', name: 'deployment_')]
final class DeploymentController extends AbstractController
{
	public function __construct(
		private readonly ValidatorInterface $validator,
		private readonly SerializerInterface $serializer,
		private readonly EntityManagerInterface $entityManager,
	) {	}

	#[Route('/{id}', methods: ['GET'], name: 'get_by_id')]
	public function getById(?Deployment $deployment): JsonResponse
	{
		($deployment) ?: throw new NotFoundHttpException('Deployment not found');

		$resultDTO = new DeploymentDTO(
			name: $deployment->getName(),
			start_date: $deployment->getStartDate()->format('Y-m-d'),
			end_date: $deployment->getEndDate()->format('Y-m-d'),
			shift_needed: $deployment->getShiftNeeded()
		);

		return new JsonResponse( $this->serializer->serialize($resultDTO, 'json', ['groups' => ['read']]));
	}

	#[Route('/{name}', methods: ['GET'], name: 'get_all_by_name')]
	public function getAllByName(?string $name, DeploymentRepository $repo): JsonResponse
	{
		$deployments = $repo->findBy(['name' => $name]);

		$result = [];
		foreach($deployments as $deployment){
			$DTO = new DeploymentDTO(
				name: $deployment->getName(),
				start_date: $deployment->getStartDate()->format('Y-m-d'),
				end_date: $deployment->getEndDate()->format('Y-m-d'),
				shift_needed: $deployment->getShiftNeeded()
			);
			$result[] = $this->serializer->serialize($DTO, 'json', ['groups' => ['read']]);
		}

		return new JsonResponse($result[]);
	}

	#[Route('', methods: ['POST'], name: 'create')]
	public function create(Request $request): JsonResponse
	{
		/** @var DeploymentDTO $serializedRequest */
		$serializedRequest = $this->serializer->deserialize(
			$request->getContent(),
			DeploymentDTO::class,
			'json',
			['groups' => ['write']]
		);

		$deployment = new Deployment(
			start_date: new DateTimeImmutable($serializedRequest->start_date),
			end_date: $serializedRequest->end_date ? new DateTimeImmutable($serializedRequest->end_date) : null,
			name: $serializedRequest->name,
			shift_needed: $serializedRequest->shift_needed,
			id: Uuid::fromString($serializedRequest->id)
		);

		$this->entityManager->persist($deployment);
		$this->entityManager->flush();

		$errors = $this->validator->validate($serializedRequest, null, ['Default', 'write']);
		count($errors) == 0 ?: throw new ValidationFailedException($serializedRequest, $errors);

		return new JsonResponse(['status' => 'Deployment created'], Response::HTTP_CREATED);
	}
}
