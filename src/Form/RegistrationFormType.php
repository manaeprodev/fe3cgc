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
        ->add('avatar', FileType::class, [
            'label' => 'Avatar (Image File)',
            'mapped' => false,
            'required' => false,
            'attr' => ['accept' => 'image/*'],
        ])
        ->add('title', EntityType::class, [
            'label' => 'Class (Gives a small buff to your army)',
            'class' => Title::class,
            'choice_label' => function (Title $title) {
                return $title->getName() . " (" . $title->getMiniLabel() . ")";
            },
            'required' => false,
            'placeholder' => 'None',
            'empty_data' => null,
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
