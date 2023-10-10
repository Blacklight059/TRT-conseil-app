<?php

namespace App\Controller;

use App\Repository\JobOffersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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


}
