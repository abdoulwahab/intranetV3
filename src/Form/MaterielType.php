<?php

namespace App\Form;

use App\Entity\Materiel;
use App\Entity\TypeMateriel;
use App\Repository\TypeMaterielRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class MaterielType extends AbstractType
{
    protected $formation;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formation = $options['formation'];

        $builder
            ->add('libelle', TextType::class, ['label' => 'label.libelle'])
            ->add('description', TextType::class, ['label' => 'label.libelle'])
            ->add('photoFile', VichFileType::class, [
                'required'       => false,
                'label'          => 'label.photo',
                'download_label' => 'label.apercu',
                'allow_delete'   => false
            ])
            ->add('typeMateriel', EntityType::class, [
                'class'         => TypeMateriel::class,
                'required'      => true,
                'choice_label'  => 'libelle',
                'query_builder' => function(TypeMaterielRepository $typeMaterielRepository) {
                    return $typeMaterielRepository->findByFormationBuider($this->formation);
                },
                'label'         => 'label.type_materiel',
                'expanded'      => false

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Materiel::class,
            'translation_domain' => 'form',
            'formation'            => null,

        ]);
    }
}
