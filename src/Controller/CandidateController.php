<?php

namespace App\Controller;

use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use App\Security\UserAuthentificatorAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;



#[Route('/candidate', name: 'candidate')]
class CandidateController extends AbstractController
{
        #[Route('/', name: 'index')]
        public function index(CandidateRepository $candidateRepository): Response
        {
            $user = $this->getUser();
            $user = $candidateRepository->findOneBy(['id' => $user->getId()]);

            return $this->render('candidate/index.html.twig', [
                'controller_name' => 'CandidateController',
                'user' => $user
            ]);
        }



        #[Route('/_edit/{id}', name: 'candidate_edit')]
        public function edit(Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        UserAuthenticatorInterface $userAuthenticator, 
        UserAuthentificatorAuthenticator $authenticator, 
        EntityManagerInterface $entityManager,
        CandidateRepository $candidateRepository,
        SluggerInterface $slugger,
        int $id=null
        ): Response
        {
        
            // We retrieve the candidate id in the url
            $user = $candidateRepository->findBy(['id' => $id])[0];

            $form = $this->createForm(CandidateType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $CVFilename */
                $CVFilename = $form->get('CVFilename')->getData();
            // this condition is needed because the 'CVFilename' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($CVFilename) {
                $originalFilename = pathinfo($CVFilename->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$CVFilename->guessExtension();

                // Move the file to the directory where CVFileNames are stored
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
