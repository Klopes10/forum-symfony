<?php 

namespace App\Controller;

use App\Entity\Tag;

use App\Entity\Sujet;
use App\Entity\Message;
use App\Form\SujetType;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/sujet")
 */

class SujetController extends AbstractController
{
    
    /**
     * @Route("/{slug?}", name="sujet")
     */
    public function index(Tag $tag=null): Response
    {
        if($tag){
            
            $sujets = $this->getDoctrine()
                ->getRepository(Sujet::class)
                ->findBy(["tag"=> $tag]);
        
        } else {
            $sujets = $this->getDoctrine()
                ->getRepository(Sujet::class)
                ->findAll();
        }

        return $this->render('sujet/index.html.twig',[
            'sujets' => $sujets,
        ]);
        
    }

    /**
    * @Route("/create/new", name="sujet_new")
    */
    public function new(Request $request, Sujet $sujet=null) {
       
        $sujet = new Sujet();

        $form = $this->createForm(SujetType::class, $sujet);
        
        $form->handleRequest($request);     //permet de gérer le traitement de la saisie du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
         
            $sujet = $form->getData();
            $sujet->setAuthor($this->getUser());
            
            $entityManager = $this->getDoctrine()->getManager(); //signale à Doctrine que l'objet doit être enregistré
            $entityManager->persist($sujet);
            $entityManager->flush(); //Met à jour la base à partir des objets signalés à Doctrine. 
                                    //Tant qu'elle n'est pas appellée, rien n'est modifié en base.

            return $this->redirectToRoute('sujet',['slug'=> $sujet->getTag()->getSlug()]);
        
        }

        return $this->render("sujet/new.html.twig",
                    ["addSujet" => $form->createView(),
                    ]);
    }
    /**
    *
    * @Route("/{id}/delete",name="sujet_delete")
    */
    public function delete(Sujet $sujet, EntityManagerInterface $manager) {
        $manager->remove($sujet);
        $manager->flush();
        
        
        $this->addFlash(
            'success',
            "Le sujet {$sujet} a bien été supprimé."
        );

        return $this->redirectToRoute('sujet',['slug'=>$sujet->getTag()->getSlug()]);
    }

    /**
     * @Route("/{id}/resolve", name="sujet_resolve")
     */
    public function resolveSujet(Sujet $sujet, EntityManagerInterface $entityManager){   
        if( $this->getUser() == $sujet->getAuthor()){
            if(!$sujet->getResolved()){
                $sujet->setResolved(true);
            }else{
                $sujet->setResolved(false);
            }

            $entityManager->flush();
        } 
 

        return $this->redirectToRoute('sujet',['slug'=>$sujet->getTag()->getSlug()]);
    }   

    /**
     * @Route("/{id}/lock", name="sujet_lock")
     * @IsGranted("ROLE_MODO")
     */
    public function lockSujet(Sujet $sujet, EntityManagerInterface $entityManager){   
        
        if(!$sujet->getLocked()){
            $sujet->setLocked(true);
        }else{
            $sujet->setLocked(false);
        }

        $entityManager->flush();
        
        return $this->redirectToRoute('sujet',['slug'=>$sujet->getTag()->getSlug()]);
    } 

    /**
    * @Route("/{slug?}/show", name="sujet_show")
    */
    public function show(Request $request, Sujet $sujet): Response
    {
        $message = new Message();
        
        $form = $this->createForm(MessageType::class, $message);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $message = $form->getData();
            $message->setAuthor($this->getUser());
            $message->setSujet($sujet);

            $entityManager = $this->getDoctrine()->getManager();   
            $entityManager->persist($message);
            $entityManager->flush();
            $this->addFlash('success', 'Le message a bien été ajouté!');

            return $this->redirectToRoute('sujet_show',
                                        ['slug'=> $sujet->getSlug()]);
        }

        return $this->render('sujet/show.html.twig',['sujet'=> $sujet, "newMessage" => $form->createView() ]);
    }

    
}