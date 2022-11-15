<?php 

namespace App\Controller;

use App\Entity\User;

use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user")
 */

class UserController extends AbstractController
{
    /**
     * @Route("/", name="user")
     * @Route("/", name="root")
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->getAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }
    /**
    * @Route("/{id}", name="user_show", methods="GET")
    */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig',['user'=> $user]);
    }
}