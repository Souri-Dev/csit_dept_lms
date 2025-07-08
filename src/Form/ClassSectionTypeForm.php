<?php

namespace App\Form;

use App\Entity\ClassSection;
use App\Entity\SchoolClass;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ClassSectionTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['hide_section_name']) {
            $builder
                ->add('sectionName');
        }

        $builder
            ->add('class', EntityType::class, [
                'class' => SchoolClass::class,
                'choice_label' => 'subjectName',
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
                'label_attr' => [
                    'class' => 'col-sm-3 col-form-label',
                ],
            ])
            ->add('students', EntityType::class, [
                'class' => Student::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-check',
                ],
                'choice_attr' => function () {
                    return ['class' => 'mb-2 gap-2'];
                },
                'label_attr' => [
                    'class' => 'col-sm-3 col-form-label',
                ],
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('s')
                        ->where(':section NOT MEMBER OF s.classSections')
                        ->setParameter('section', $options['section']);
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClassSection::class,
            'hide_section_name' => false, // new option default
            'section' => null,
        ]);
    }
}
