<?php

namespace App\Controller;

use App\Entity\Consultant;
use App\Form\ConsultantType;
use App\Repository\ConsultantRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Security\UserAuthentificatorAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin', name: 'admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ConsultantRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users
        ]);
    }
    
    #[Route('/_remove/{id}', name: 'remove')]

    public function remove(EntityManagerInterface $entityManager, ConsultantRepository $userRepository, int $id): Response
    {
        // On récupère le consultant qui correspond à l'id passé dans l'URL
        $user = $userRepository->findBy(['id' => $id])[0];

        // Le consultant est supprimé
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }

    #[Route('/add', name: 'admin_add')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthentificatorAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
    
        $user = new Consultant();

        $form = $this->createForm(ConsultantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_CONSULTANT']);
            
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            );
        }

        return $this->render('admin/add.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    
    #[Route('/_edit/{id}', name: 'edit')]
    public function edit(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, ConsultantRepository $userRepository, Consultant $user, HttpFoundationRequest $request, int $id=null): Response
    {

        // Si un identifiant est présent dans l'url alors il s'agit d'une modification
        // Dans le cas contraire il s'agit d'une création d'article
        // On récupère l'offre qui correspond à l'id passé dans l'url
        $user = $userRepository->findBy(['id' => $id])[0];

    
        $form = $this->createForm(ConsultantType::class, $user);
            $user = $form->getData();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
        }


        if($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_CONSULTANT']);

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('admin');
        }

        $parameters = array(
            'form'      => $form->createView(),
            'user'      => $user,
        );

        return $this->render('admin/edit.html.twig', $parameters);

    }

}
