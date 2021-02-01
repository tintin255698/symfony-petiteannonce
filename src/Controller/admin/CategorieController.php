<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Form\EditCategoriesType;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/categories", name="admin_categories_")
 * @package App\Controller
 */
class CategorieController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('admin/categories/home.html.twig', [
            'cat' => $categoriesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajoutCategorie(Request $request): Response
    {
        $categorie = new Categories();

        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }

        return $this->render('admin/categories/index.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("edit/{slug}", name="edit")
     */
    public function editCategorie(Categories $categories, Request $request): Response
    {
        $form = $this->createForm(EditCategoriesType::class, $categories);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($categories);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
}
