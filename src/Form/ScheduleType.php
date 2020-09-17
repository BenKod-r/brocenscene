<?php

namespace App\Form;

use App\Entity\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startMorning', TimeType::class, [
                'label' => 'Matin début',
                'input' => 'datetime',
                'widget' => 'choice',
            ])
            ->add('endMorning', TimeType::class, [
                'label' => 'Matin fin',
                'input' => 'datetime',
                'widget' => 'choice',
            ])
            ->add('startAfternoon', TimeType::class, [
                'label' => 'Aprés-midi début',
                'input' => 'datetime',
                'widget' => 'choice',
            ])
            ->add('endAfternoon', TimeType::class, [
                'label' => 'Aprés-midi fin',
                'input' => 'datetime',
                'widget' => 'choice',
            ])
            ->add('opening', ChoiceType::class, [
                'label' => 'Conditions d\'ouvertures',
                'choices' => [
                    'Ouvert toute la journée' => 'Open',
                    'Ouvert le matin' => 'Open AM',
                    'Ouvert l\'aprés-midi' => 'Open PM',
                    'Fermé toute la journée' => 'Close',
                ],
            ])
            ->add('meet', ChoiceType::class, [
                'label' => 'Conditions de rendez-vous',
                'choices' => [
                    'Sans rendez-vous' => 'Not meet',
                    'Sur rendez-vous toute la journée' => 'Meet',
                    'Sur rendez-vous le matin' => 'Meet AM',
                    'Sur rendez-vous l\'aprés-midi' => 'Meet PM',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
