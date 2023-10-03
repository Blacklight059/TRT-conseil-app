<?php

namespace App\Controller;

use Amp\Http\Client\Request;
use App\Entity\User;
use App\Repository\CandidateRepository;
use App\Repository\ConsultantRepository;
use App\Repository\JobOffersRepository;
use App\Repository\RecruiterRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/consultant', name: 'consultant')]
class ConsultantController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        JobOffersRepository $jobOffersRepository,
        RecruiterRepository $recruiterRepository,
        CandidateRepository $candidateRepository
    ): Response
    {
        $offersID = $jobOffersRepository->findAll();
        $Recruiter = $recruiterRepository->findAll();
        $candidate = $candidateRepository->findAll();
        $offersValidate = $jobOffersRepository->findBy(array('validationJob' => true));
        $offersApplication = $candidateRepository->findOneBy(['application' => true]);
        $candidateApplication = $candidateRepository->findBy(array('application' => true));
        $offersValidateConsultant = $candidateRepository->findOneBy(['consultantValidate' => true]);
        $candidateValidateConsultant = $candidateRepository->findBy(array('consultantValidate' => true));

        return $this->render('consultant/index.html.twig', [
            'controller_name' => 'ConsultantController',
            'offersID' => $offersID,
            'recruiters' => $Recruiter,
            'candidates' => $candidate,
            'offersValidate' => $offersValidate,
            'userID' => $offersApplication,
            'candidateApplication' => $candidateApplication,
            'offersValidateConsultant' => $offersValidateConsultant,
            'candidateValidateConsultant' => $candidateValidateConsultant
        ]);
    }

    #[Route('/consultant_remove/{id}', name: 'remove')]

    public function remove(EntityManagerInterface $entityManager, UserRepository $userRepository, int $id): Response
    {
        // On récupère l'article qui correspond à l'id passé dans l'URL
        $user = $userRepository->findBy(['id' => $id])[0];

        // L'article est supprimé
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }
    #[Route('/consultant_add', name: 'new')]
    #[Route('/consultant_edit/{id}', name: 'consultant_edit')]
    public function edit(EntityManagerInterface $entityManager, UserRepository $userRepository, User $user, Request $request, int $id=null): Response
    {

        // Si un identifiant est présent dans l'url alors il s'agit d'une modification
        // Dans le cas contraire il s'agit d'une création d'article
        if($id) {
            $mode = 'update';
            // On récupère l'offre qui correspond à l'id passé dans l'url
            $user = $userRepository->findBy(['id' => $id])[0];

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

        return $this->render('admin/edit.html.twig', $parameters);
    }
    
    #[Route('/validation_recruiter/{id}', name: 'validation_recruiter')]
    public function validation_recruiter(
        EntityManagerInterface $entityManager,
        RecruiterRepository $recruiterRepository,
        int $id=null
    ): Response
    {

        $recruiterID = $recruiterRepository->findBy(['id' => $id])[0];

        $recruiterID->setValidation(true);

        $entityManager->persist($recruiterID);
        $entityManager->flush();
        return $this->redirectToRoute('consultant');

        return $this->render('consultant/index.html.twig', [
            'controller_name' => 'ConsultantController',
            'recruiterID' => $recruiterID,
        ]);
    }

    #[Route('/validation_candidate/{id}', name: 'validation_candidate')]
    public function validation_candidate(
        EntityManagerInterface $entityManager,
        CandidateRepository $candidateRepository,
        int $id=null
    ): Response
    {

        $candidateID = $candidateRepository->findBy(['id' => $id])[0];

        $candidateID->setValidation(true);

        $entityManager->persist($candidateID);
        $entityManager->flush();
        return $this->redirectToRoute('consultant');

        return $this->render('consultant/index.html.twig', [
            'controller_name' => 'ConsultantController',
            'candidateID' => $candidateID,
        ]);
    }

    #[Route('/validationJob/{id}', name: 'validationJob')]
    public function validationJob(
        EntityManagerInterface $entityManager, 
        JobOffersRepository $jobOffersRepository, 
        int $id=null
    ): Response
    { 
        $offers = $jobOffersRepository->findBy(['id' => $id])[0];
        $offers->setValidationJob(true);

        $entityManager->persist($offers);
        $entityManager->flush();
        return $this->redirectToRoute('consultant');
        
        return $this->render('consultant/index.html.twig', [
            'controller_name' => 'ConsultantController',
            'offers' => $offers
        ]);
    }

    #[Route('/validation_apply/{id}', name:'validation_apply')]
    public function validation_apply(
        JobOffersRepository $jobOffersRepository
        ): Response
    {
        $offers = $jobOffersRepository->findBy(array('validationJob' => true));
        dd($offers);
        return $this->render('consultant/index.html.twig', [
            'controller_name' => 'ConsultantController',
            'offers' => $offers
        ]);
    }
    #[Route('/_email/{id1}/{id2}', name:'email') ]
    public function sendEmail(
        EntityManagerInterface $entityManager, 
        MailerInterface $mailer,
        ConsultantRepository $consultantRepository,
        JobOffersRepository $jobOffersRepository,
        CandidateRepository $candidateRepository,
        int $id1=null,
        int $id2=null,
        ): Response
    {
        $offersID = $jobOffersRepository->findAll();
        $consultant = $this->getUser();
        $consultantID = $consultantRepository->find($consultant);
        $offers = $jobOffersRepository->findBy(['id' => $id1])[0];
        $candidate = $candidateRepository->find(['id' => $id2]);
        $recruiter = $offers->getRecruiter()->getEmail();
        $offers->setConsultanValidate(true);
        $candidate->setConsultantValidate(true);

        $entityManager->persist($candidate, $offers);
        $entityManager->flush();
        $email = (new Email())
            ->from($consultantID->getEmail())
            ->to($recruiter)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Candidat selectionné pour votre offre d\'emploi')
            ->text('Nom :'. $candidate->getLastname() . 'prénom :' . $candidate->getFirstname())
            ->html('<p>Voici un candidat qui a repondu à vos critères</p>')
            ->addPart(new DataPart(fopen('../public/uploads/CVFilename/'.$candidate->getCVFilename(), 'r')));
        $mailer->send($email);
        return $this->redirectToRoute('consultant');
    }
}
