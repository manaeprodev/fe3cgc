<?php

namespace App\Form;

use App\Entity\Title;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $avatars = [
            'Mercenary' => 'FE16_Generic_Male_Mercenary_Portrait.webp',
            'Swordmaster' => 'FE16_generic_male_Swordmaster_Portrait.webp',
            'Assassin' => 'FE16_Generic_Male_Assassin.webp',
            'War Master' => 'Goneril_portrait.webp',
            'Great Knight' => 'Chilon_portrait.webp',
            'Grappler' => 'FE16_generic_grappler_portrait.webp',
            'Trickster' => 'FE3HTrickster.webp',
            'Sniper' => 'FEW3H_Berling_Portrait.webp',
            'Bow Knight' => 'Riegan_portrait.webp',
            'Holy Knight' => 'Daphnel_portrait.webp',
            'Dark Knight' => 'Blaiddyd_portrait.webp',
            'Pegasus Knight' => 'Dominic_portrait.webp',
            'Falcon Knight' => 'Fraldarius_portrait.webp',
            'Bishop' => 'FE16_generic_female_Bishop_Portrait.webp',
            'Dark Bishop' => 'Odesse_Dark_Bishop_portrait.webp',
            'Sage' => 'Gloucester_portrait.webp',
            'Warlock' => 'FE16_Generic_Female_Warlock_Portrait.webp',
            'Mortal Savant' => 'Mortal_Savant_Charon_portrait.webp',
            'Gremory' => 'Gremory_portrait.webp',
            'Dark Mage' => 'Myson_portrait.webp',
        ];

        $randomAvatar = array_rand($avatars);

        $builder
        ->add('username', TextType::class, [
            'label' => 'Username',
            'attr' => ['placeholder' => 'Enter your username'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a username',
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'Your username should be at least {{ limit }} characters',
                    'max' => 50,
                ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email Address',
            'attr' => ['placeholder' => 'Enter your email'],
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'label' => 'Agree to Terms and Conditions ',
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
        ])
        ->add('plainPassword', PasswordType::class, [
            'label' => 'Password',
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Enter a password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('confirmPassword', PasswordType::class, [
            'label' => 'Confirm Password',
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Confirm your password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please confirm your password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('avatar', ChoiceType::class, [
            'label' => 'User Avatar',
            'choices' => $avatars,
            'multiple' => false, // Un seul choix possible
            'required' => true,
            'data' => $avatars[$randomAvatar], // Valeur par défaut aléatoire
        ])
        ->add('title', EntityType::class, [
            'label' => 'Class (Gives a small buff to your army)',
            'class' => Title::class,
            'choice_label' => function (Title $title) {
                return $title->getName() . " (" . $title->getMiniLabel() . ")";
            },
            'required' => true,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('t')
                    ->where('t.level = :level')
                    ->setParameter('level', 1);
                },
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
