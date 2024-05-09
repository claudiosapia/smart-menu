<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Add this line
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class RegistrationController extends AbstractController
{
    #[Route('/reg', name: 'reg')]
    public function reg(
        Request $request,
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator
    ): Response {
        $regform = $this->createFormBuilder()
            ->add('username', TextType::class, ['label' => 'Username'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password']
            ])
            ->add('register', SubmitType::class)
            ->getForm();

        $regform->handleRequest($request);

        if ($regform->isSubmitted() && $regform->isValid()) {
            $input = $regform->getData();
            $user = new User();
            $user->setUsername($input['username']);

            // Encode the password
            $encodedPassword = $passwordHasher->hashPassword($user, $input['password']);
            $user->setPassword($encodedPassword);

            $user->setRawPassword($encodedPassword);
            $errors = $validator->validate($user);
            if(count($errors) > 0){
                return $this->render('registration/index.html.twig', [
                    'regform' => $regform->createView(),
                    'errors' => $errors
                ]);
             } else{
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('home');
             }
      

        }

        // Render the form
        return $this->render('registration/index.html.twig', [
            'regform' => $regform->createView(),
             'errors' => null
        ]);
    }
}