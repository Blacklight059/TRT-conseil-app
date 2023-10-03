<?php

namespace App\Controller;

use App\Entity\Recruiter;
use App\Form\RecruiterType;
use App\Repository\JobOffersRepository;
use App\Security\UserAuthentificatorAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/recruiter', name: 'recruiter')]
class RecruiterController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(JobOffersRepository $jobOffersRepository): Response
    {
        $offers = $jobOffersRepository->findAll();        

        return $this->render('recruiter/index.html.twig', [
            'controller_name' => 'RecruiterController',
            'offers' => $offers,
        ]);
    }

    #[Route('/add', name: 'recruiter_add')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthentificatorAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
    
        $user = new Recruiter();

        $form = $this->createForm(RecruiterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_RECRUITER']);
            
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            );
        }

        return $this->render('recruiter/add.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
