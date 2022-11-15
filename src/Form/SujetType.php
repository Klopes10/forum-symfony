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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SujetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class,[ 
                              
                'attr'=> ['class' => 'uk-text', 'rows' => 8],
                'constraints' =>[
                new NotNull([
                    'message' => 'Veuillez donner un titre à ce sujet',
                ]),
            ],
        ])
            ->add('tag', EntityType::class,[
                'class' => Tag::class,
                'choice_label' => 'nomTag',
            ])          

            ->add('Valider', SubmitType::class,[
                'attr'=> ['class'=> 'uk-button uk-button-secondary uk-margin-top uk-margin-bottom']
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sujet::class,
        ]);
    }
}