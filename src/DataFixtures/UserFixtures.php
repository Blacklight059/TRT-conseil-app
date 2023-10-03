<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Candidate;
use App\Entity\Consultant;
use App\Entity\Recruiter;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

use Faker;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ){}

    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $admin->setEmail('admin@admin.fr');
        $admin->setFirstname("admin");
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $recruiter = new Recruiter();
        $recruiter->setEmail('recruiter@recruiter.fr');
        $recruiter->setCompanyName('recruiter');
        $recruiter->setLastname('recruiter');
        $recruiter->setFirstname('recruiter');
        $recruiter->setAddress('14, rue du recruiter');
        $recruiter->setZipcode('59000');
        $recruiter->setCity('recruiter');
        $recruiter->setPassword(
            $this->passwordEncoder->hashPassword($recruiter, 'recruiter')
        );
        $recruiter->setRoles(['ROLE_RECRUITER']);
        $manager->persist($recruiter);

        $candidate = new Candidate();
        $candidate->setEmail('candidate@candidate.fr');
        $candidate->setLastname('candidate');
        $candidate->setFirstname('candidate');
        $candidate->setAddress('14, rue du candidate');
        $candidate->setZipcode('59000');
        $candidate->setCity('candidate');
        $candidate->setPassword(
            $this->passwordEncoder->hashPassword($candidate, 'candidate')
        );
        $candidate->setRoles(['ROLE_CANDIDATE']);
        $manager->persist($candidate);

        $consultant = new Consultant();
        $consultant->setEmail('consultant@consultant.fr');
        $consultant->setLastname('consultant');
        $consultant->setFirstname('consultant');
        $consultant->setAddress('14, rue du consultant');
        $consultant->setZipcode('59000');
        $consultant->setCity('consultant');
        $consultant->setPassword(
            $this->passwordEncoder->hashPassword($consultant, 'consultant')
        );
        $consultant->setRoles(['ROLE_CONSULTANT']);
        $manager->persist($consultant);


        $faker = Faker\Factory::create('fr-FR');

        for($usr = 1; $usr <= 5; $usr++) {
            $candidate = new Candidate();
            $candidate->setEmail($faker->email);
            $candidate->setLastname($faker->lastname);
            $candidate->setFirstname($faker->firstname);
            $candidate->setAddress($faker->streetAddress);
            $candidate->setZipcode(substr(str_replace(' ', '', $faker->postcode), 0, 5));
            $candidate->setCity($faker->city);
            $candidate->setPassword(
            $this->passwordEncoder->hashPassword($candidate, 'secret')
            );
            $candidate->setRoles(['ROLE_CANDIDATE']);
            $manager->persist($candidate);

        }
        for($usr2 = 1; $usr2 <= 5; $usr2++) {
            $user = new Recruiter();
            $user->setEmail($faker->email);
            $user->setCompanyName($faker->lastname);
            $user->setLastname($faker->lastname);
            $user->setFirstname($faker->firstname);
            $user->setAddress($faker->streetAddress);
            $user->setZipcode(substr(str_replace(' ', '', $faker->postcode), 0, 5));
            $user->setCity($faker->city);
            $user->setPassword(
            $this->passwordEncoder->hashPassword($user, 'secret')
            );
            $user->setRoles(['ROLE_RECRUITER']);
            $manager->persist($user);

        }

        for($usr3 = 1; $usr3 <= 5; $usr3++) {
            $consultant = new Consultant();
            $consultant->setEmail($faker->email);
            $consultant->setLastname($faker->lastname);
            $consultant->setFirstname($faker->firstname);
            $consultant->setAddress($faker->streetAddress);
            $consultant->setZipcode(substr(str_replace(' ', '', $faker->postcode), 0, 5));
            $consultant->setCity($faker->city);
            $consultant->setPassword(
            $this->passwordEncoder->hashPassword($consultant, 'secret')
            );
            $consultant->setRoles(['ROLE_CONSULTANT']);
            $manager->persist($consultant);

        }
        $manager->flush();
    }
}
