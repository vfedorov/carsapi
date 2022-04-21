<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
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
class CarsController extends AbstractController
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
	 * @param CarRepository $carRepository
	 * @param SerializerInterface $serializer
	 * @return Response
	 */
	#[Route('/cars', name: 'get_cars', methods: 'GET')]
	public function getCars(CarRepository $carRepository, SerializerInterface $serializer): Response {
		$cars = $carRepository->findAll();
		$result = [];
		foreach ($cars as $car) {
			array_push($result, $car->toArray());
		}
		$data = $serializer->serialize($result, 'json');
		return new Response($data, Response::HTTP_OK);
	}

	/**
	 * @param int $id
	 * @param CarRepository $carRepository
	 * @return Response
	 */
	#[Route('/car/{id}', name: 'get_car', methods: 'GET')]
	public function getCar(int $id, CarRepository $carRepository): Response {
		$car = $carRepository->find($id);
		if (empty($car)) {
			return new JsonResponse(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);
		}
		return new JsonResponse($car->toArray(), Response::HTTP_OK);
	}

	/**
	 * @param int $id
	 * @param CarRepository $carRepository
	 * @return Response
	 */
	#[Route('/cars/{id}', name: 'delete_car', methods: 'DELETE')]
	public function deleteCar(int $id, CarRepository $carRepository): Response {
		$car = $carRepository->find($id);
		if (empty($car)) {
			return new JsonResponse(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);
		}
		$this->em->remove($car);
		$this->em->flush();
		return new JsonResponse(['message' => 'Car successfully removed'], Response::HTTP_OK);
	}

	/**
	 * @param Request $request
	 * @param ColourRepository $colourRepository
	 * @param SerializerInterface $serializer
	 * @return Response
	 * @throws \Exception
	 */
	#[Route('/cars', name: 'add_car', methods: 'POST')]
	public function addCars(Request $request, ColourRepository $colourRepository, SerializerInterface $serializer): Response {
		$constraints = new Assert\Collection([
			'make' => [new Assert\NotBlank()],
			'model' => [new Assert\NotBlank()],
			'build_date' => [
				new Assert\Date(),
				new Assert\LessThanOrEqual((new \DateTime('now'))->format('Y-m-d')),
				new Assert\GreaterThan((new \DateTime('now'))->modify('-4 year')->format('Y-m-d')),
			],
			'colour' => [new Assert\NotBlank()],
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

		$colour = $colourRepository->findOneBy(['name' => strtolower($requestData['colour'])]);
		if (!$colour) {
			return new JsonResponse(['message' => 'Wrong colour'], Response::HTTP_BAD_REQUEST);
		}

		$car = new Car();
		$car->setMake($requestData['make']);
		$car->setModel($requestData['model']);
		$car->setBuildAt(new \DateTime($requestData['build_date']));
		$car->setColour($colour);

		$this->em->persist($car);
		$this->em->flush();

		return new JsonResponse($car->toArray(), Response::HTTP_OK);
	}
}
