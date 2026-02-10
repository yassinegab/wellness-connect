<?php

namespace App\Form;

use App\Entity\Front_office\RendezVous;
use App\Entity\Front_office\User;
use App\Entity\Front_office\Hopital;
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
                'choice_label' => fn(User $user) => $user->getNom().' '.$user->getPrenom(),
                'label' => 'Patient',
                'placeholder' => 'Sélectionnez un patient',
                'required' => false,
            ])

            ->add('medecin', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $user) => 'Dr. '.$user->getNom().' '.$user->getPrenom(),
                'label' => 'Médecin',
                'placeholder' => 'Sélectionnez un médecin',
                'required' => false,
            ])

            ->add('hopital', EntityType::class, [
                'class' => Hopital::class,
                'choice_label' => 'nom',
                'label' => 'Hôpital',
                'placeholder' => 'Sélectionnez un hôpital',
                'required' => false,
            ])

     
            ->add('dateRendezVous', DateTimeType::class, [
                'label' => 'Date et heure du rendez-vous',
                'widget' => 'single_text',
                'input' => 'datetime', // 🔥 FIX PRINCIPAL
                'required' => false,
            ])

            ->add('typeConsultation', ChoiceType::class, [
                'label' => 'Type de consultation',
                'choices' => [
                    'Présentiel' => 'Présentiel',
                    'Téléconsultation' => 'Téléconsultation',
                    'Urgence' => 'Urgence',
                ],
                'placeholder' => 'Choisissez un type',
                'required' => false,
            ])

            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'En attente',
                    'Confirmé' => 'Confirmé',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé',
                ],
                'data' => 'En attente',
                'required' => false,
            ])

            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Notes supplémentaires...',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
