<?php

namespace App\Form;

use App\Entity\Contrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civilite', ChoiceType::class, [
                'choices'  => [
                    'Monsieur' => 'Monsieur',
                    'Madame' => 'Madame',
                    'Non-binaire' => 'non-binaire',
                ],
            ])
            ->add('prenom')
            ->add('nom')
            ->add('dateNaissance')
            ->add('nomNaissance')
            ->add('paysNaissance')
            ->add('email')
            ->add('adresse')
            ->add('codePostale')
            ->add('ville')
            ->add('telephone')
            ->add('situation', ChoiceType::class, [
                'choices'  => [
                    'Prémium Réserviste' => 'Prémium Réserviste',
                    'Aspirant' => 'Aspirant',
                ],
            ])
            ->add('nbReserviste')
            ->add('dateDelivrance')
            ->add('reservisteFiles', FileType::class, [
                'label' => 'Votre pièce d\identité',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ->add('dateGarantie')
            ->add('signatureId')
            ->add('documentId')
            ->add('signerId')
            ->add('pdfNoFirm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
