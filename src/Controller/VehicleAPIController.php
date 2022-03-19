<?php

namespace App\Controller;

use DateTime;
use App\Entity\Vehicle;
use VehicleNotFoundException;
use OpenApi\Annotations as OA;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

#[Route(path: "/api/v1/vehicle", name: "vehicle_")]
class VehicleAPIController extends AbstractController
{
    /**
     * @var ContainerBagInterface
     */
    private ContainerBagInterface $params;

    /**
     * VehicleAPIController Construct
     *
     * @param ContainerBagInterface $params
     *
     * @return void
     */
    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * Get all vehicles
     *
     * Return all vehicles that are not deleted from the database
     *
     * @Route(
     *     path="/",
     *     name="all",
     *     methods={"GET"}
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns all vehicles",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Vehicle::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="keyword",
     *     in="query",
     *     description="Partial or whole text to search in make and model",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="sort",
     *     in="query",
     *     description="The field and direction to sort results",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="offset",
     *     in="query",
     *     description="offset to apply",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="limit of results",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="column",
     *     in="query",
     *     description="Specific column to search for the keyword text",
     *     @OA\Schema(type="string")
     * )
     */
    public function all(VehicleRepository $vehicle, Request $req): Response
    {

        $column = $req->query->get('column', 'full');
        $sort = explode(',', $req->query->get('sort', 'id,desc'));
        $keyword = $req->query->get('keyword', null);
        $offset = $req->query->get('offset', 0);
        $limit = $req->query->get('limit', 10);

        $data = $vehicle->findByKeyword($keyword, $sort, $column, $offset, $limit, $this->params->get('app.used_only'));

        return $this->json($data);
    }

    /**
     * Get a vehicles
     *
     * Return a single vehicle from a provided id
     *
     * @Route(
     *     path="/{id}",
     *     name="show",
     *     methods={"GET"}
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a single vehicle",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Vehicle::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The id of the vehicle to be returned",
     *     @OA\Schema(type="integer")
     * )
     */
    public function show(VehicleRepository $vehicle, int $id): Response
    {
        $data = $vehicle->findById($id, $this->params->get('app.used_only'));

        if ($data) {
            return $this->json($data);
        } else {
            throw new VehicleNotFoundException($id);
        }
    }


    /**
     * Add Vehicle
     *
     * Add a new vehicle to the database
     *
     *@Route(
     *     path="",
     *     name="create",
     *     methods={"POST"}
     * )
     *@OA\Response(
     *     response=200,
     *     description="Returns created vehicle",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Vehicle::class, groups={"full"}))
     *     )
     * )
     */
    public function create(Request $request, ValidatorInterface $validator, EntityManagerInterface $manager): Response
    {
        $required = ['type', 'vin', 'model', 'make', 'msrp', 'miles'];

        foreach ($required as $req) {
            if (!$request->get('make') || empty($request->get('make'))) {
                return $this->json(['message' => 'Missing Required Field: '.$req], 422);
            }
        }

        $vehicle = new Vehicle();
        $vehicle->setMake($request->get('make'));
        $vehicle->setModel($request->get('model'));
        $vehicle->setType($request->get('type'));
        $vehicle->setMsrp((float)$request->get('msrp', 0));
        $vehicle->setVin($request->get('vin'));
        $vehicle->setMiles((int)$request->get('miles', 0));
        $vehicle->setYear((int)$request->get('year'));
        $vehicle->setDateAdded($request->get('date', (new DateTime())));

        $errors = $validator->validate($vehicle);

        if (count($errors) > 0) {
            return new Response((string) $errors, 422);
        }

        $manager->persist($vehicle);
        $manager->flush();

        return $this->json($vehicle, 201);
    }

   /**
     * Update Vehicle
     *
     * Update a vehicles information
     *
     * @Route(
     *     path="/{id}",
     *     name="update",
     *     methods={"PATCH"}
     * )
     */
    public function update(VehicleRepository $vehicle, int $id, Request $request, ValidatorInterface $validator, EntityManagerInterface $manager): Response
    {
        $data = json_decode($request->getContent(), true);

        $fields = ['type', 'vin', 'model', 'make', 'msrp', 'miles'];

        $vehicle = $vehicle->findById($id, $this->params->get('app.used_only'));
        
        $i = 0;
        foreach ($fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $m = 'set'.Ucwords($field);
                $vehicle->{$m}($data[$field]);
                $i++;
            }
        }

        if ($i == 0) {
            return $this->json(['message' => 'No Data has been updated'], 202);
        }

        
        $errors = $validator->validate($vehicle);

        if (count($errors) > 0) {
            return new Response((string) $errors, 422);
        }

        $manager->persist($vehicle);
        $manager->flush();

        return $this->json($vehicle, 202);
    }

    /**
     * Delete Vehicle
     *
     * Update a vehicles information
     *
     * @Route(
     *     path="/{id}",
     *     name="delete",
     *     methods={"DELETE"}
     * )
     */
    public function delete(VehicleRepository $vehicle, int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $vehicle = $vehicle->findById($id, $this->params->get('app.used_only'));
        $manager->remove($vehicle);
        $manager->flush();

        return new $this->json(['status' => 'Vehicle deleted'], 204);
    }
}
