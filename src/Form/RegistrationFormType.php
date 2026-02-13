<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new Assert\NotBlank(['message' => 'Le nom est obligatoire'])],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [new Assert\NotBlank(['message' => 'Le prénom est obligatoire'])],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire']),
                    new Assert\Email(['message' => 'L\'email doit être valide']),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false, // On gère le hash dans le controller
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mot de passe est obligatoire']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [new Assert\NotBlank(['message' => 'Le téléphone est obligatoire'])],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Patient' => UserRole::PATIENT->value,
                    'Médecin' => UserRole::MEDECIN->value,
                    'Admin' => UserRole::ADMIN->value,
                ],
                'placeholder' => '-- Choisir un rôle --',
                'constraints' => [new Assert\NotBlank(['message' => 'Le rôle est obligatoire'])],
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Âge',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'âge est obligatoire']),
                    new Assert\Positive(['message' => 'L\'âge doit être positif']),
                ],
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
                'placeholder' => '-- Choisir le sexe --',
                'constraints' => [new Assert\NotBlank(['message' => 'Le sexe est obligatoire'])],
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Poids (kg)',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le poids est obligatoire']),
                    new Assert\Positive(['message' => 'Le poids doit être positif']),
                ],
            ])
            ->add('taille', NumberType::class, [
                'label' => 'Taille (cm)',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La taille est obligatoire']),
                    new Assert\Positive(['message' => 'La taille doit être positive']),
                ],
            ])
            ->add('handicap', CheckboxType::class, [
                'label' => 'Handicap',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
