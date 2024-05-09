<?php

namespace App\Controller;
use App\Entity\Dish;
use App\Entity\Order;
use App\Entity\Status;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface; // Import the EntityManagerInterface
class OrderController extends AbstractController
{
    private $entityManager; // Declare the EntityManager

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/order', name: 'order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders= $orderRepository->findBy([
            'ordertable' => 'dish1']);
  
        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/order/{id}', name: 'single_order')]
    public function orders(Dish $dish, Request $request): Response
    {
        try {
            $order = new Order();
            $order->setOrdertable("dish1");
            $order->setName($dish->getName());
            $order->setPrice($dish->getPrice());
            $order->setOrderNumber($dish->getId());
            $order->setStatus("open");
    
            $this->entityManager->persist($order);
            $this->entityManager->flush();
    
            $this->addFlash('order', 'Order created successfully!');
   } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while creating the order.');
        }
    
        return $this->redirectToRoute('menu');
    }


    #[Route('/status/{id}/{status?}', name: 'status')]
    public function status($id, $status ) {

        $order = $this->entityManager->getRepository(Order::class)->find($id);
        $order->setStatus($status);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
      
return $this->redirectToRoute('order');

 }



 #[Route('/cancel/{id}', name: 'cancel')]
 public function delete($id, OrderRepository $orderRepository)
 {
     // Find the dish with the specified ID
     $order = $orderRepository->find($id);
 
     // If the order is not found, throw a NotFoundException
     if (!$order) {
         throw $this->createNotFoundException('order not found.');
     }
 
     // Remove the order from the EntityManager
     $this->entityManager->remove($order);
 
     // Save the changes to the database
     $this->entityManager->flush();
    
     //message
     $this->addFlash('success', 'order deleted successfully!');
     // Redirect to the 'dishes.edit' route after successful deletion
     return $this->redirectToRoute('order');
 }






}
