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


}
