<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\User;
use App\Form\AnnoncesContactType;
use App\Repository\AnnoncesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @Route("/annonce", name="annonce_")
 */
class AnnonceController extends AbstractController
{
    /**
     * @Route("/detail/{slug}", name="detail")
     */
    public function details(AnnoncesRepository $annoncesRepository, Annonces $annonces, $slug, Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(AnnoncesContactType::class);

        $contact = $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid() ){
            $mail = (new TemplatedEmail())
                ->from($contact->get('email')->getData())
                ->to($annonces->getUsers()->getEmail())
                ->subject("Contact au sujet de votre annonce" . $annonces->getTitle() )
                ->htmlTemplate('emails/contact_annonce.html.twig')
                ->context([
                    'annonce' =>$annonces,
                    'mail'=> $contact->get('email')->getData(),
                    'message'=>$contact->get('message')->getData()
                ]);
            $mailer->send($mail);
            $this->addFlash('message', 'Votre email a bien ete envoye');
            return $this->redirectToRoute('annonce_detail', [$slug => $annonces->getSlug()]);

        }

        return $this->render('annonce/index.html.twig', [
            'detail' => $annoncesRepository->findOneBy(['slug'=> $slug]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/favoris/add/{id}", name="favoris_add")
     */
    public function favoris(Annonces $annonces): Response
    {
        $annonces->addFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonces);
        $em->flush();

       return new Response('add');
    }

    /**
     * @Route("/favoris/delete/{id}", name="favoris_delete")
     */
    public function delete(Annonces $annonces): Response
    {
        $annonces->removeFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonces);
        $em->flush();

        return new Response('remove');
    }
}
