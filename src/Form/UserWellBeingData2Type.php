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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserWellBeingData::class,
        ]);
    }
}
