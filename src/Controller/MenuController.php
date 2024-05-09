<?php

namespace App\Controller;
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    public function index(DishRepository $ds): Response
    {
        $dishes = $ds->findAll();

        return $this->render('menu/index.html.twig', [
            'dish' => $dishes,
        ]);
    }
}
