<?php

namespace App\Controller;

use App\Form\AnnoncesContactType;
use App\Form\ContactType;
use App\Repository\AnnoncesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("", name="app_home")
     */
    public function index(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'annonce' => $annoncesRepository->findBy(['active' => true], ['created_at' => 'DESC']),
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(MailerInterface $mailer, Request $request): Response
    {
        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid() ){
            $mail = (new TemplatedEmail())
                ->from($contact->get('email')->getData())
                ->to('noreply@gmail.com')
                ->subject("Contact")
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'mail'=> $contact->get('email')->getData(),
                    'sujet'=>$contact->get('sujet')->getData(),
                    'message'=>$contact->get('message')->getData()
                ]);
            $mailer->send($mail);
            $this->addFlash('message', 'Votre email a bien ete envoye');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
