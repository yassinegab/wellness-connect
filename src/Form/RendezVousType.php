<?php

namespace App\Form;

use App\Entity\RendezVous;
use App\Entity\Hopital;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getFullName() . ' (' . $user->getEmail() . ')';
                },
                'label' => 'Patient',
                'placeholder' => 'Sélectionnez un patient',
                'attr' => ['class' => 'form-select'],
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_PATIENT%')
                        ->orderBy('u.nom', 'ASC');
                },
            ])
            ->add('hopital', EntityType::class, [
                'class' => Hopital::class,
                'choice_label' => 'nom',
                'label' => 'Hôpital',
                'placeholder' => 'Sélectionnez un hôpital',
                'attr' => ['class' => 'form-select']
            ])
            ->add('typeConsultation', ChoiceType::class, [
                'label' => 'Type de consultation',
                'choices' => [
                    'Présentiel' => 'Présentiel',
                    'Téléconsultation' => 'Téléconsultation',
                    'Urgence' => 'Urgence',
                ],
                'placeholder' => 'Choisissez le type',
                'attr' => ['class' => 'form-select']
            ])
            ->add('dateRendezVous', DateTimeType::class, [
                'label' => 'Date et heure du rendez-vous',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes / Symptômes',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Décrivez vos symptômes ou ajoutez des notes...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}