<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\JobOffers;
use App\Form\JobOffersFormType;
use App\Repository\CandidateRepository;
use App\Repository\JobOffersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/joboffers', name: 'joboffers')]

class JobOffersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(JobOffersRepository $jobOffersRepository): Response
    {
        $offers = $jobOffersRepository->findAll();
        $userID = $this->getUser();

        return $this->render('joboffers/index.html.twig', [
            'controller_name' => 'JobOffersController',
            'offers' => $offers,
            'userID' => $userID
        ]);
    }
    #[Route('/_remove/{id}', name: 'remove')]

    public function remove(EntityManagerInterface $entityManager, JobOffersRepository $jobOffersRepository, int $id): Response
    {
        // On récupère l'article qui correspond à l'id passé dans l'URL
        $jobOffers = $jobOffersRepository->findBy(['id' => $id])[0];
        $userID = $this->getUser();

        
        $entityManager->remove($jobOffers);
        $entityManager->flush();
        if($userID->getRoles() == 'ROLE_CONSULTANT') {
            return $this->redirectToRoute('consultant');

        } else {
            return $this->redirectToRoute('joboffers');
        }

    }
    
    #[Route('/_add', name: 'add')]
    #[Route('/_edit/{id}', name: 'edit')]
    public function edit(EntityManagerInterface $entityManager, JobOffersRepository $jobOffersRepository, UserRepository $userID, JobOffers $jobOffers, HttpFoundationRequest $request, int $id=null): Response
    {

        // Si un identifiant est présent dans l'url alors il s'agit d'une modification
        // Dans le cas contraire il s'agit d'une création d'article
        $userID = $this->getUser();
        if($id) {
            $mode = 'update';
            // On récupère l'offre qui correspond à l'id passé dans l'url
            $jobOffers = $jobOffersRepository->findBy(['id' => $id])[0];

        }
        else {
            $mode = 'new';
            $jobOffers = new JobOffers();
            $jobOffers->setRecruiter($userID);

        }
        $form = $this->createForm(JobOffersFormType::class, $jobOffers);
            $jobOffers = $form->getData();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
        }


        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobOffers);
            $entityManager->flush();
            return $this->redirectToRoute('joboffers');
        }

        $parameters = array(
            'form'      => $form->createView(),
            'jobOffers' => $jobOffers,
            'mode'      => $mode,
            'userID'    => $userID

        );

        return $this->render('jobOffers/edit.html.twig', $parameters);
    }
    #[Route('/_applyjob/{id}', name: 'applyjob')]
    public function applyjob(
        EntityManagerInterface $entityManager, 
        JobOffersRepository $jobOffersRepository, 
        CandidateRepository $candidateRepository,
        int $id=null
        ): Response
    {

        $user = $this->getUser();
        // On récupère l'offre qui correspond à l'id passé dans l'url
        $jobOffers = $jobOffersRepository->findBy(['id' => $id])[0];
        $jobOffers->addCandidate($user);
        $user = $candidateRepository->find($this->getUser());
        $user->addOffer($jobOffers);
        $user->setApplication(true);

        $entityManager->persist($jobOffers, $user);
        $entityManager->flush();

        return $this->redirectToRoute('Homepage');

        return $this->render('Homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'userID' => $user,
            'jobOffers' => $jobOffers,
        ]);
    }

}
