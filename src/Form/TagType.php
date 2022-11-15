<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Sujet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomTag', TextType::class,[ 
                              
                'attr'=> ['class' => 'uk-text', 'rows' => 8],
                'constraints' =>[
                new NotNull([
                    'message' => 'Veuillez donner un nom à ce tag',
                ]),
            ],
        ])

        ->add('Valider', SubmitType::class,[
            'attr'=> ['class'=> 'uk-button uk-button-secondary uk-margin-top uk-margin-bottom']
        ])
    ;
    ;

    }

}