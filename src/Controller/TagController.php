<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/tag")
 */

class TagController extends AbstractController
{
    /**
     * @Route("/", name="tag")
     */
    public function index(): Response
    {
        $tags = $this->getDoctrine()
                ->getRepository(Tag::class)
                ->getAll();

        return $this->render('tag/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
    * @Route("/create/new", name="tag_new")
    */
    public function new(Request $request, Tag $tag=null) {
        if (!$tag) {
            $tag = new Tag();
        }
        $form = $this->createForm(TagType::class, $tag);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $tag = $form->getData();
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('tag');
        
        }

        return $this->render("tag/new.html.twig",
                    ["addTag" => $form->createView()]);
    }
    
   
}
