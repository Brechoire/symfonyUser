<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class EditPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'class' => 'form-control'
                ],
                'mapped' => false,
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Nouveau mot de passe *',
                    'attr' => ['class' => 'form-control']
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe *',
                    'attr' => ['class' => 'form-control']
                ],
                'invalid_message' => 'Le mot de passe n\'est pas identique',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit comporter au moins 6 caractères.',
                        'max' => 255,
                        'maxMessage' => 'Le mot de passe doit comporter un maximum de 255 caractères.'
                    ]),
                    new NotNull(['message' => 'Mot de passe requis']),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
