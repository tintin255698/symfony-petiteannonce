<?php

namespace App\Controller\admin;

use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/annonces", name="admin_annonces_")
 * @package App\Controller
 */
class AnnonceController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('admin/annonces/home.html.twig', [
            'annonce' => $annoncesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(Annonces $annonces): Response
    {
        $annonce = $annonces->setActive(($annonces->getActive()?false:true));

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        return new Response('true');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Annonces $annonces): Response
    {
        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->remove($annonces);
        $doctrine->flush();

        $this->addFlash('message','Annonce supprimee avec succes');
        return $this->redirectToRoute('admin_annonces_home');
    }

}
