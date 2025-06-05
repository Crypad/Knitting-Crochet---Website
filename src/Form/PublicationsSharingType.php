<?php

namespace App\Form;

use App\Entity\Models;
use App\Entity\PublicationsSharing;
use App\Entity\Tags;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationsSharingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('images')
            ->add('content')
            ->add('likes')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
            ->add('models', EntityType::class, [
                'class' => Models::class,
'choice_label' => 'id',
'multiple' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tags::class,
'choice_label' => 'id',
'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublicationsSharing::class,
        ]);
    }
}
