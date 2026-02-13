<?php

namespace App\Form;

use App\Entity\DossierMedical;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DossierMedicalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('antecedentsMedicaux', TextareaType::class, [
                'label' => 'Antécédents Médicaux',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez les antécédents médicaux du patient...'
                ]
            ])
            ->add('maladiesChroniques', TextareaType::class, [
                'label' => 'Maladies Chroniques',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Listez les maladies chroniques...'
                ]
            ])
            ->add('allergies', TextareaType::class, [
                'label' => 'Allergies',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Décrivez les allergies connues...'
                ]
            ])
            ->add('traitementsEnCours', TextareaType::class, [
                'label' => 'Traitements en Cours',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Listez les traitements actuels avec dosages...'
                ]
            ])
            ->add('diagnostics', TextareaType::class, [
                'label' => 'Diagnostics',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez les diagnostics établis...'
                ]
            ])
            ->add('notesMedecin', TextareaType::class, [
                'label' => 'Notes du Médecin',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Remarques et observations du médecin...'
                ]
            ])
            ->add('objectifSante', TextareaType::class, [
                'label' => 'Objectif de Santé',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Définissez les objectifs de santé...'
                ]
            ])
            ->add('niveauActivite', ChoiceType::class, [
                'label' => 'Niveau d\'Activité',
                'choices' => [
                    'Sédentaire' => 'Sédentaire',
                    'Modéré' => 'Modéré',
                    'Actif' => 'Actif',
                    'Très Actif' => 'Très Actif'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('medecin', EntityType::class, [
                'label' => 'Médecin Responsable',
                'class' => User::class,
                'choice_label' => 'fullName',
                'placeholder' => 'Sélectionner un médecin',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'MEDECIN')
                        ->orderBy('u.prenom', 'ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DossierMedical::class,
        ]);
    }
}
