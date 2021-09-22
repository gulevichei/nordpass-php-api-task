<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Service\ItemService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ItemController
 *
 * @package App\Controller
 */
class ItemController extends AbstractController
{
    /**
     * @Route("/item", name="item_list", methods={"GET"})
     * @IsGranted("ROLE_USER")
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        // return the data array without converting it into an object
        // also to speed up, we can cache the results in redis - for example
        $allItems = $this->getDoctrine()
            ->getRepository(Item::class)
            ->findItemsByUser($this->getUser());

        return $this->json($allItems);
    }

    /**
     * @Route("/item", name="item_create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     *
     * @param Request     $request
     * @param ItemService $itemService
     *
     * @return JsonResponse
     */
    public function create(Request $request, ItemService $itemService): JsonResponse
    {
        $data = $request->get('data');

        if (empty($data)) {
            // we should return status code Response::HTTP_BAD_REQUEST
            return $this->json(['error' => 'No data parameter']);
        }

        $itemService->create($this->getUser(), $data);
        // we should return status code 201 JsonResponse::HTTP_CREATED
        return $this->json([]);
    }

    /**
     * @Route("/item", name="item_update", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $id = (int)$request->get('id');
        if (empty($id)) {
            return $this->json(['error' => 'No data parameter'], Response::HTTP_BAD_REQUEST);
        }

        $data = $request->get('data');
        if (empty($data)) {
            return $this->json(['error' => 'No data parameter'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Item $item */
        $item = $this->getDoctrine()->getRepository(Item::class)
            ->findOneBy(['id' => $id]);

        if ($item === null) {
            return $this->json(['error' => 'No item'], Response::HTTP_NOT_FOUND);
        }

        // check so that only your item can be update
        if ($item->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Access Deny'], Response::HTTP_FORBIDDEN);
        }

        $item->setData($data);
        return $this->json([]);
    }

    /**
     * @Route("/item/{id}", name="items_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     *
     * @param int     $id
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        if (empty($id)) {
            return $this->json(['error' => 'No data parameter'], Response::HTTP_BAD_REQUEST);
        }

        $item = $this->getDoctrine()->getRepository(Item::class)->find($id);

        if ($item === null) {
            return $this->json(['error' => 'No item'], Response::HTTP_BAD_REQUEST);
        }

        // check so that only your item can be deleted
        if ($item->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Access Deny'], Response::HTTP_FORBIDDEN);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($item);
        $manager->flush();

        return $this->json([]);
    }
}
