<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Recruiter;
use App\Form\CandidateType;
use App\Form\RecruiterType;
use App\Repository\CandidateRepository;
use App\Repository\JobOffersRepository;
use App\Security\UserAuthentificatorAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(
        JobOffersRepository $jobOffersRepository,
        CandidateRepository $candidateRepository): Response
    {
        $user = $this->getUser();
        $candidate = false;
        
        $offers = $jobOffersRepository->findBy(array('validationJob' => true));
        if($user != null) {
            $user = $this->getUser()->getRoles()[0]; 
            $candidate = $candidateRepository->find($this->getUser());
            if($user === 'ROLES_CANDIDATE') {
                $candidate = $candidate->isApplication(); 
            }
        }

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'offers' => $offers,
            'user' => $user,
            'candidate' => $candidate
        ]);
    }
    
    #[Route('/recruiter_add', name: 'recruiter_add')]
    public function recruiter_add(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthentificatorAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
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

    #[Route('/candidate_add', name: 'candidate_add')]
    public function candidate_add(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        UserAuthenticatorInterface $userAuthenticator, 
        UserAuthentificatorAuthenticator $authenticator, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response
    {
    
        $user = new Candidate();

        $form = $this->createForm(CandidateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $CVFilename */
            $CVFilename = $form->get('CVFilename')->getData();
                        // this condition is needed because the 'CVFileName' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($CVFilename) {
            $originalFilename = pathinfo($CVFilename->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$CVFilename->guessExtension();

            // Move the file to the directory where CVFilenames are stored
            try {
                $CVFilename->move(
                    $this->getParameter('CVFilenames_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'CVFilename' property to store the PDF file name
            // instead of its contents
            $user->setCVFilename($newFilename);
        }
            // encode the plain password

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_CANDIDATE']);
            
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            );
        }

        return $this->render('candidate/add.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
