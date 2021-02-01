<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/annonce", name="annonce_")
 */
class AnnonceController extends AbstractController
{
    /**
     * @Route("/detail/{slug}", name="detail")
     */
    public function details(AnnoncesRepository $annoncesRepository, $slug): Response
    {
        return $this->render('annonce/index.html.twig', [
            'detail' => $annoncesRepository->findOneBy(['slug'=> $slug]),
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
