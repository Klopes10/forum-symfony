<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('texte', TextareaType::class,[
                'label'=> " ",
                'attr'=> ['class' => 'uk-textarea', 'rows' => 8],
                'constraints' =>[
                    new NotNull([
                        'message' => 'Please enter a message',
                    ]),
                ],
                'required' => false
            ])

            ->add('Valider', SubmitType::class,[
                'attr'=> ['class'=> 'uk-button uk-button-secondary uk-margin-top uk-margin-bottom']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}