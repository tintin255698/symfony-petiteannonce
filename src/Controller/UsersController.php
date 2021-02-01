<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Images;
use App\Entity\User;
use App\Form\AnnoncesType;
use App\Form\UsersModifyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/users/annonces/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonces(Request $request): Response
    {
        $annonce = new Annonces();

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            $images = $form->get('images')->getData();
            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier);

                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }

            $em=$this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/index.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/users/profil/edit/{id}", name="users_profil_edit")
     */
    public function editProfil(Request $request): Response
    {
        $id = $this->getUser();

        $form = $this->createForm(UsersModifyType::class, $id);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($id);
            $em->flush();

            $this->addFlash('message', 'Profil mis a jour');

            return $this->redirectToRoute('users');
        }

        return $this->render('users/edit.html.twig', [
            'form'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/users/pass/edit/{id}", name="users_pass_edit")
     */
    public function editPass(User $user, PasswordEncoderInterface $passwordEncoder, Request $request): Response
    {
        $id = $this->getUser();

       if($request->isMethod("POST")){
           if($request->request->get("pass") == $request->request->get("pass2")){
               $em = $this->getDoctrine()->getManager();
               $user->setPassword($passwordEncoder->encodePassword($id, $request->request->get("pass")));
               $em->flush();
               $this->addFlash('success', 'Mot de passe modifie');
           }
           else {
               $this->addFlash('error', 'Mot de passe different');
           }
            return $this->redirectToRoute('users');
        }
        return $this->render('users/edit.html.twig', [
        ]);
    }



}
