<?php

namespace App\Controller;

use App\DTO\CreateShift;
use App\Enum\DeploymentStatus;
use App\Entity\Shift;
use App\Repository\ShiftRepository;
use App\Repository\DeploymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[Route('/api/shift', name: 'shift_')]
class ShiftController extends AbstractController
{
    #[Route('', methods: ['GET'], name: 'list')]
    public function list(Request $request, ShiftRepository $repo): JsonResponse
    {
        $statusParam = $request->query->get('status');
        $sortParam = strtoupper($request->query->get('sort', 'ASC'));

        $validStatuses = array_map(fn(DeploymentStatus $status) => $status->value, DeploymentStatus::cases());

        if (!$statusParam || !in_array($statusParam, $validStatuses, true)) {
            return new JsonResponse(
                ['error' => 'Missing or invalid "status" parameter. Valid: active, archived, locked'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $status = DeploymentStatus::from($statusParam);
        $direction = $sortParam === 'DESC' ? 'DESC' : 'ASC';

        $shifts = $repo->findByDeploymentStatusSorted($status, $direction);

        $result = [];
        foreach ($shifts as $shift) {
            $result[] = [
                'id' => $shift->getId(),
                'date' => $shift->getDate()->format('Y-m-d H:i:s'),
                'worked' => $shift->getWorked(),
                'pay' => $shift->getPay(),
            ];
        }

        return new JsonResponse($result);
    }

    #[Route('', methods: ['POST'], name: 'create')]
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        DeploymentRepository $deploymentRepo
    ): JsonResponse {
        /** @var CreateShift $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateShift::class, 'json', ['groups' => ['write']]);

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }

        $deployment = $deploymentRepo->find($dto->deployment_id);
        if (!$deployment) {
            return new JsonResponse(['error' => 'Deployment not found'], Response::HTTP_NOT_FOUND);
        }

        if ($deployment->getShift()->count() >= $deployment->getShiftNeeded()) {
            return new JsonResponse(['error' => 'Deployment has reached its shift limit'], Response::HTTP_BAD_REQUEST);
        }

        $shift = new Shift(
            deployment: $deployment,
            date: new \DateTimeImmutable($dto->date),
            worked: $dto->worked,
            pay: $dto->pay,
        );

        $em->persist($shift);
        $em->flush();

        return new JsonResponse(['status' => 'Shift created'], Response::HTTP_CREATED);
    }
}
