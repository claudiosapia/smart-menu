<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class MailerController extends AbstractController
{
    #[Route('/mail', name: 'mail')]
    public function sendMail(MailerInterface $mailer, Request $request): Response
    {
        $emailForm = $this->createFormBuilder()
            ->add('text', TextareaType::class, [
                'attr' => ['rows' => '5']
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $formData = $emailForm->getData();
            $text = $formData['text'];
            $dish = 'dish1';

            $email = (new TemplatedEmail())
                ->from('tisch1@menucart.wip')
                ->to('tisch1@menucart.wip')
                ->subject('Order!')
                ->text('extra fries')
                ->htmlTemplate('mailer/mailer.html.twig')
                ->context([
                    'dish' => $dish,
                    'text' => $text
                ]);

            $mailer->send($email);
            $this->addFlash('success', 'Order placed!');
        }

        return $this->render('mailer/index.html.twig', [
            'emailForm' => $emailForm->createView()
        ]);
    }
}