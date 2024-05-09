<?php

namespace App\Controller;

use App\Entity\Dish;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface; // Import the EntityManagerInterface

use App\Form\DishType;
use App\Repository\DishRepository;

#[Route('/dishes', name: 'dishes.')]
class DishesController extends AbstractController
{
    private $entityManager; // Declare the EntityManager

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'edit')]
    public function index(DishRepository $dishRepository)
    {
        $dishes = $dishRepository->findAll();

        return $this->render('dishes/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

   
    #[Route('/create', name: 'create')]
    
public function create(Request $request): Response
{
    $dish = new Dish();
    $form = $this->createForm(DishType::class, $dish);
    $form->handleRequest($request);

 
    if ($form->isSubmitted() && $form->isValid()) {
     
        $imageFile = $form->get('image')->getData();
    
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            // Get the extension of the original file
            $originalExtension = pathinfo($imageFile->getClientOriginalName(), PATHINFO_EXTENSION);
    
            $safeFilename = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $originalFilename));
            $newFilename = $safeFilename.'-'.uniqid().'.'.$originalExtension;
    
          
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
         
    
            $dish->setImage($newFilename);
        }
    
 


        $this->entityManager->persist($dish);
        $this->entityManager->flush();

        return $this->redirectToRoute('dishes.edit');
    }

    return $this->render('dishes/create.html.twig', [
        'createForm' => $form->createView(),
    ]);
}
    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, DishRepository $dishRepository)
    {
        // Find the dish with the specified ID
        $dish = $dishRepository->find($id);
    
        // If the dish is not found, throw a NotFoundException
        if (!$dish) {
            throw $this->createNotFoundException('Dish not found.');
        }
    
        // Remove the dish from the EntityManager
        $this->entityManager->remove($dish);
    
        // Save the changes to the database
        $this->entityManager->flush();
       
        //message
        $this->addFlash('success', 'Dish deleted successfully!');
        // Redirect to the 'dishes.edit' route after successful deletion
        return $this->redirectToRoute('dishes.edit');
    }


#[Route('/show/{id}', name: 'show')]
public function show(Dish $dish)
{
    
 return $this->render('dishes/show.html.twig', [
     'dish' => $dish,
 ]);
  
}







#[Route('/price/{id}', name: 'price')]
public function price($id, DishRepository $dishRepository)
{
    $dish = $dishRepository->find5gbp($id);

   dump($dish);

}



}