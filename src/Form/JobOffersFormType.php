<?php

namespace App\Form;

use App\Entity\JobOffers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class JobOffersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                // Titre de l'offre d'emploi
                ->add('title', TextareaType::class, [
                    'label' => 'Titre'
                ])
                // Nom de la société
                ->add('companyName', TextareaType::class, [
                    'label' => 'Société'
                ])
                // Adresse
                ->add('address', TextareaType::class, [
                    'label' => 'Adresse'
                ])
        
                // code postale du poste
                ->add('zipcode', TextareaType::class, [
                    'label' => 'Code postale'
                ])
                //Ville
                ->add('city', TextareaType::class, [
                    'label' => 'ville'
                ])
                // Description
                ->add('description', TextareaType::class, [
                    'label' => 'description'
                ])
                // Bouton Envoyer
                ->add('submit', SubmitType::class, array(
                    'label' => 'Enregistrer'
                ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobOffers::class,
        ]);
    }
}
