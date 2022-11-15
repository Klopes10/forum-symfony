<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Sujet;
use App\Form\EditUserType;
use App\Repository\SujetRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/admin")
*/

class AdminController extends AbstractController
{
    /**
    * @Route("/", name="index")
    */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**************************************************************PARTIE GESTION UTILISATEUR****************************************************************/
    



    /**
     * Liste les utilisateurs du site
     * @Route("/utilisateurs", name= "utilisateurs")
     */
    public function usersList(UserRepository $users){
        return $this->render("admin/users.html.twig",[
            'users' => $users->findAll()
        ]);

    }
    
    /**
     * Modifier un utilisateur
     *
     * @Route("/utilisateurs/modifier/{id}",name="modifier_utilisateur")
     */
    public function editUser(User $user, Request $request){
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('utilisateurs');
        }
        
        return $this->render('admin/edituser.html.twig',[
            'userForm' => $form->createView()
        ]);
    }



    

/************************************************************* GESTION TAGs ***********************************************************************/


    /**
     * @Route("/tags", name="tags")
     */
    public function listeTags(TagRepository $tags): Response
    {
        $tags = $this->getDoctrine()
                ->getRepository(Tag::class)
                ->findAll();

        return $this->render('admin/tags.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
    *
    * @Route("/{id}/delete",name="tag_delete")
    */
    public function deleteTag(Tag $tag, EntityManagerInterface $manager) {
        $manager->remove($tag);
        $manager->flush();
        

        return $this->redirectToRoute('tags',['slug'=>$tag->getSlug()]);
    }

    /************************************************************************* GESTION SUJETS *********************************************************/

    /**
    * @Route("/sujets", name="sujets")
    */
    public function getAll(SujetRepository $sujets): Response  
    {
        $sujets = $this->getDoctrine()
                    ->getRepository(Sujet::class)
                    ->getAll();

        return $this->render('admin/sujets.html.twig', [
            'sujets' => $sujets,
        ]);
    }

    
    /******************************************************* GESTION MESSAGES***************************/

    /**
    * @Route("/{slug?}/messages", name="messages", methods="GET")
    */
    public function show(Sujet $sujet): Response
    {
        return $this->render('admin/messages.html.twig',['sujet'=> $sujet]);
    }

  

    
    /********************************************************POUR PLUS TARD******************************************************************/

    /**
    * @Route("/utilisateurs/delete/{id}",name="delete_utilisateur")
    */

    public function delete(User $user, EntityManagerInterface $manager) {
        $manager->remove($user);
        $manager->flush();
        
        
        $this->addFlash(
            'success',
            "Le membre a bien été banni."
        );

        return $this->redirectToRoute('utilisateurs');
    }
    
    /*public function index(): Response
    {
        $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->getAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }*/

   

   
}
