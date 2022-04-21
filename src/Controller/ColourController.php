<?php

namespace App\Controller;

use App\Entity\Colour;
use App\Repository\ColourRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class ColourController extends AbstractController
{
	/**
	 * @var ValidatorInterface
	 */
	private ValidatorInterface $validator;
	/**
	 * @var ObjectManager
	 */
	private ObjectManager $em;

	/**
	 * @param ManagerRegistry $doctrine
	 * @param ValidatorInterface $validator
	 */
	public function __construct(ManagerRegistry $doctrine, ValidatorInterface $validator)
	{
		$this->validator = $validator;
		$this->em = $doctrine->getManager();
	}

	/**
	 * @param ColourRepository $colourRepository
	 * @param SerializerInterface $serializer
	 * @return Response
	 */
	#[Route('/colours', name: 'get_colours', methods: 'GET')]
	public function getColours(ColourRepository $colourRepository, SerializerInterface $serializer): Response {
		$colours = $colourRepository->findAll();
		$result = [];
		foreach ($colours as $colour) {
			array_push($result, $colour->toArray());
		}
		$data = $serializer->serialize($result, 'json');
		return new Response($data, Response::HTTP_OK);
	}

	/**
	 * @param int $id
	 * @param ColourRepository $colourRepository
	 * @return Response
	 */
	#[Route('/colours/{id}', name: 'delete_colour', methods: 'DELETE')]
	public function deleteColour(int $id, ColourRepository $colourRepository): Response {
		$colour = $colourRepository->find($id);
		if (empty($colour)) {
			return new JsonResponse(['message' => 'Colour not found'], Response::HTTP_NOT_FOUND);
		}
		if (!$colour->getEditable()) {
			return new JsonResponse(['message' => 'This colour cannot be removed'], Response::HTTP_NOT_FOUND);
		}
		$this->em->remove($colour);
		$this->em->flush();
		return new JsonResponse(['message' => 'Colour successfully removed'], Response::HTTP_OK);
	}

	/**
	 * @param Request $request
	 * @param SerializerInterface $serializer
	 * @return Response
	 */
	#[Route('/colours', name: 'add_colour', methods: 'POST')]
	public function addColour(Request $request, SerializerInterface $serializer): Response {
		$constraints = new Assert\Collection([
			'name' => [new Assert\NotBlank()],
		]);

		$requestData = $request->request->all();

		$errors = $this->validator->validate($requestData, $constraints);

		if (count($errors) > 0) {
			$errorMessages = [];
			foreach ($errors as $e) {
				$errorMessages[$e->getPropertyPath()][] = $e->getMessage();
			}
			$response = new JsonResponse();
			$response->setContent($serializer->serialize(['messages' => $errorMessages], 'json'));
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			return $response;
		}

		$colour = new Colour();
		$colour->setName($requestData['name']);
		$colour->setEditable(true);

		$this->em->persist($colour);
		$this->em->flush();

		return new JsonResponse($colour->toArray(), Response::HTTP_OK);
	}

	/**
	 * @param int $id
	 * @param Request $request
	 * @param ColourRepository $colourRepository
	 * @param SerializerInterface $serializer
	 * @return Response
	 */
	#[Route('/colours/{id}/edit', name: 'edit_colour', methods: 'POST')]
	public function editColour(int $id, Request $request, ColourRepository $colourRepository, SerializerInterface $serializer): Response {
		$colour = $colourRepository->find($id);
		if (empty($colour)) {
			return new JsonResponse(['message' => 'Colour not found'], Response::HTTP_NOT_FOUND);
		}
		if (!$colour->getEditable()) {
			return new JsonResponse(['message' => 'This colour cannot be edited'], Response::HTTP_NOT_FOUND);
		}

		$constraints = new Assert\Collection([
			'name' => [new Assert\NotBlank()],
		]);

		$requestData = $request->request->all();

		$errors = $this->validator->validate($requestData, $constraints);

		if (count($errors) > 0) {
			$errorMessages = [];
			foreach ($errors as $e) {
				$errorMessages[$e->getPropertyPath()][] = $e->getMessage();
			}
			$response = new JsonResponse();
			$response->setContent($serializer->serialize(['messages' => $errorMessages], 'json'));
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			return $response;
		}

		$colour->setName($requestData['name']);

		$this->em->persist($colour);
		$this->em->flush();
		return new JsonResponse($colour->toArray(), Response::HTTP_OK);
	}
}
