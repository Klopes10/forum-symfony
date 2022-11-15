<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Sujet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        $tags = $this->getDoctrine()
        ->getRepository(Tag::class)
        ->getAll();

        $tagswithsujets=[];

        foreach($tags as $tag){
            $tagwithsujets=[
                'tag' => $tag,
                'sujets' => $this->getDoctrine()
                ->getRepository(Sujet::class)
                ->findBy(['tag'=> $tag],
                ['creation_date' => 'DESC'],
                3)
            ];
            $tagswithsujets[] = $tagwithsujets;
        }

        return $this->render('home/index.html.twig', [
            'tagswithsujets' => $tagswithsujets,
        ]);
    }


}
