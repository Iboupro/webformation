<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\CourseCategory;
use App\Entity\CourseLevel;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',textType::class,[
                'label' => 'Nom du cours',
            ])
            ->add('smallDescription',TextType::class,[
                'label' => 'Une petite description du cours',
            ])
            ->add('fullDescription',textareaType::class,[
                'label' => 'Une description compléte du cours',
            ])
            ->add('duration',TextType::class,[
                'label' => 'durée du cours',
            ])
            ->add('price', NumberType::class, [
                'scale' => 2,
                'label' => 'Prix',])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'Publier le cours',
                'required' => false,
            ])
            ->add('imageFile',VichImageType::class, [
                'required'      => false,
                'allow_delete'  => false,
                'download_uri'  => false,
                'asset_helper'  => true,
                'label'         => 'Image du cours',
            ])
            ->add('programFile',VichFileType::class, [
                'required' => false,
                'allow_delete' => false,
                'asset_helper' => true,
                'label' => 'Programme',
            ])
            ->add('schedule', ChoiceType::class, [
                'label' => 'Mois de formation',
                'placeholder' => 'Choisir un mois',
                'choices' => [
                    'Janvier' => 'Janvier',
                    'Février' => 'Février',
                    'Mars' => 'Mars',
                    'Avril' => 'Avril',
                    'Mai' => 'Mai',
                    'Juin' => 'Juin',
                    'Juillet' => 'Juillet',
                    'Août' => 'Août',
                    'Septembre' => 'Septembre',
                    'Octobre' => 'Octobre',
                    'Novembre' => 'Novembre',
                    'Décembre' => 'Décembre',
                ],
            ])
            ->add('categoryId', EntityType::class, [
                'label' => 'Categorie',
                'class' => CourseCategory::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir une categorie',
            ])
            ->add('levelId', EntityType::class, [
                'label' => 'Niveau',
                'class' => CourseLevel::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir une niveau',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
