<?php

namespace App\Controller;

use Amp\Http\Client\Request;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user_remove/{id}', name: 'remove')]

    public function remove(EntityManagerInterface $entityManager, UserRepository $userRepository, int $id): Response
    {
        // On récupère l'article qui correspond à l'id passé dans l'URL
        $user = $userRepository->findBy(['id' => $id])[0];

        // L'article est supprimé
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }

    #[Route('/user_edit/{id}', name: 'user_edit')]
    public function edit(EntityManagerInterface $entityManager, UserRepository $userRepository, User $user, Request $request, int $id=null): Response
    {
            $user = $userRepository->findBy(['id' => $id])[0];
            dd($user);
        // Si un identifiant est présent dans l'url alors il s'agit d'une modification
        // Dans le cas contraire il s'agit d'une création d'article
        if($id) {
            $mode = 'update';
            // On récupère l'offre qui correspond à l'id passé dans l'url

        }
        else {
            $mode = 'new';
            $user = new User();
            $roles[] = 'ROLE_CONSULTANT';      
        }
        $form = $this->createForm(UserType::class, $user);
            $user = $form->getData();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
        }


        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('admin');
        }

        $parameters = array(
            'form'      => $form->createView(),
            'user'      => $user,
            'mode'      => $mode
        );

        return $this->redirectToRoute('admin');
    }

}
