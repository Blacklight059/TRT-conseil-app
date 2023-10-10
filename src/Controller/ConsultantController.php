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
    
    #[Route('/validation_recruiter/{id}', name: 'validation_recruiter')]
    public function validation_recruiter(
        EntityManagerInterface $entityManager,
        RecruiterRepository $recruiterRepository,
        int $id=null
    ): Response
    {

        // We retrieve the recruiter who corresponds to the id passed in the URL
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
        // We retrieve the candidate who corresponds to the id passed in the URL
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
        // We retrieve the jobOffer who corresponds to the id passed in the URL
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
        // We retrieve the job offer which corresponds to true in validationjob
        $offers = $jobOffersRepository->findBy(array('validationJob' => true));
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
        // We retrieve the connected consultant using the ID
        $consultant = $this->getUser();
        $consultantID = $consultantRepository->find($consultant);

        // We retrieve the connected job offer using the ID
        $offers = $jobOffersRepository->findBy(['id' => $id1])[0];

        // We retrieve the connected candidate using the ID
        $candidate = $candidateRepository->find(['id' => $id2]);

        // We retrieve the recruiter's email
        $recruiter = $offers->getRecruiter()->getEmail();

        // We pass the validation to true
        $offers->setConsultanValidate(true);
        $candidate->setConsultantValidate(true);

        $entityManager->persist($candidate, $offers);
        $entityManager->flush();
        $email = (new Email())
            ->from($consultantID->getEmail())
            ->to($recruiter)
            ->subject('Candidat selectionné pour votre offre d\'emploi')
            ->text('Nom :'. $candidate->getLastname() . 'prénom :' . $candidate->getFirstname())
            ->html('<p>Voici un candidat qui a repondu à vos critères</p>')
            ->addPart(new DataPart(fopen('../public/uploads/CVFilename/'.$candidate->getCVFilename(), 'r')));
        $mailer->send($email);
        return $this->redirectToRoute('consultant');
    }
}
