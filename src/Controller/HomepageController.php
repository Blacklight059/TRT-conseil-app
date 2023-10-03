<?php

namespace App\Controller;

use App\Repository\CandidateRepository;
use App\Repository\JobOffersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

}
