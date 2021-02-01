<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 *
 * @Route ("/todos",methods={"OPTIONS"})
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function index(TodoRepository $repository): Response
    {
        $response = $this->json($repository->findAll());

        return $response;
    }

    /**
     *
     * @Route ("/{id}" ,methods={"GET"})
     */
    public function show($id, EntityManagerInterface $em)
    {
        return $this->json($em->find(Todo::class, $id));

    }

    /**
     * @Route ("/" ,methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $request->request->add(["todo" => json_decode($request->getContent(), true)]);
        $form = $this->createForm(TodoType::class, new Todo())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($form->getData());
            $em->flush();
            return $this->json($form->getData(), 201);
        }

        return $this->json(["error" => "BAD request"], 400);

    }

    /**
     * @param Todo $todo
     * @Route ("/{id}" ,methods={"DELETE"})
     */
    public function delete($id, EntityManagerInterface $em)
    {
        $entity = $em->find(Todo::class, $id);
        if ($entity) {
            $em->remove($entity);
            $em->flush();
            return $this->json(["message" => "success"], 205);
        }
        return $this->json(["message" => "NOt Fund"], 404);
    }
}
