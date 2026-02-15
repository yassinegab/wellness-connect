<?php

namespace App\Form;

use App\Entity\UserWellBeingData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserWellBeingData2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('workEnvironment')
            ->add('sleepProblems')
            ->add('headaches')
            ->add('restlessness')
            ->add('heartbeatPalpitations')
            ->add('lowAcademicConfidence')
            ->add('classAttendance')
            ->add('anxietyTension')
            ->add('irritability')
            ->add('subjectConfidence')
            ->add('createdAt')
            ->add('user', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => \App\Entity\User::class,
                'choice_label' => 'email',
                'placeholder' => 'Select a User',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserWellBeingData::class,
        ]);
    }
}
