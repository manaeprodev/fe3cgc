<?php

namespace App\Form;

use App\Entity\Faction;
use App\Entity\Territory;
use App\Entity\Title;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('username')
            ->add('avatar')
            ->add('renown')
            ->add('money')
            ->add('firstCo')
            ->add('title', EntityType::class, [
                'class' => Title::class,
                'choice_label' => 'id',
            ])
            ->add('faction', EntityType::class, [
                'class' => Faction::class,
                'choice_label' => 'id',
            ])
            ->add('territory', EntityType::class, [
                'class' => Territory::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
