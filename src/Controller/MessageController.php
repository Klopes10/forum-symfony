<?php 

namespace App\Controller;

use App\Entity\Sujet;
use App\Entity\Message;

use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/message")
 */

class MessageController extends AbstractController
{

    /**
    * @Route("/new/{slug}", name="message_new") 
    */
    public function new( Sujet $sujet,Request $request) {       //  ? => nullable
       
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

        return $this->render("message/new.html.twig",
                    ["newMessage" => $form->createView(),
                    ]);
    }

    /**
     * @Route("/{id}/edit", name="message_edit")
     */
    public function edit( Message $message, Request $request, EntityManagerInterface $entityManager){

        $form = $this->createForm(MessageType::class, $message);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($message);
                $entityManager->flush();
                $this->addFlash('success', 'Le message a bien été modifier!');

                return $this->redirectToRoute('sujet_show',
                                            ['slug'=> $message->getSujet()->getSlug()]);

            }

        return $this->render(
                    "message/new.html.twig",
                    ["newMessage" => $form->createView()]);
    }
        

    /**
    * @Route("/{id}/delete",name="message_delete")
    */

    public function delete(Message $message, EntityManagerInterface $manager) {
        $manager->remove($message);
        $manager->flush();
        
        
        $this->addFlash(
            'sucess',
            "Le commentaire a bien été supprimé."
        );

        return $this->redirectToRoute('sujet_show',['slug'=>$message->getSujet()->getSlug()]);
    }

}