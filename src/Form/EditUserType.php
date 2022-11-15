<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('pseudo')
            ->add('email', EmailType::class, [
                'constraints' =>[
                    new NotBlank([
                        'message' => 'Merci de saisir une adresse email'
                    ])
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ]) */
            ->add('roles', ChoiceType::class,[
                'choices' => [
                    'Membre' =>'ROLE_USER',
                    'Modérateur' => 'ROLE_MODO',
                ],
                
                "expanded" => true,     // true => on aura des boutons radio sinon ça sera un "bloc" de selection
                "multiple" => true,     // possibilité de donner plusieurs rôles
                "label" => 'Rôles'      // label => nom choisit à un champ de formulaire
            ])
            ->add('Valider', SubmitType::class)
                
           
    

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
