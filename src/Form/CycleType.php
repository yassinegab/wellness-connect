<?php

namespace App\Form;

use App\Entity\Cycle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
    $builder
    ->add('dateDebutM', DateType::class, [
        'widget' => 'single_text',
        'attr' => ['id' => 'cycle_start'] // <-- add this
    ])
    ->add('dateFinM', DateType::class, [
        'widget' => 'single_text',
        'attr' => ['id' => 'cycle_end'] // <-- add this
    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cycle::class,
        ]);
    }
}
