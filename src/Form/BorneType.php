<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Form/BorneType.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

namespace App\Form;

use App\Entity\Borne;
use App\Entity\Semestre;
use App\Form\Type\DateRangeType;
use App\Form\Type\YesNoType;
use App\Repository\SemestreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BorneType
 * @package App\Form
 */
class BorneType extends AbstractType
{
    private $departement;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->departement = $options['departement'];

        $builder
            ->add('icone', ChoiceType::class, [
                'attr'        => ['data-provide' => 'selectpicker'],
                'label'       => 'label.icone',
                'choices'     => Borne::ICONES,
                'choice_attr' => static function($choiceValue, $key, $value) {
                    return ['data-icon' => Borne::ICONES[$key] . ' mr-2']; //todo à voir comment récupérer value et pas l'icine
                },
            ])
            ->add('couleur', TextType::class, [
                'label' => 'label.couleur',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'label.message',
            ])
            ->add('url', TextType::class, [
                'label'    => 'label.url',
                'required' => false
            ])
            ->add('dateRange', DateRangeType::class, ['label' => 'dateRange', 'mapped' => false, 'required' => true])
            ->add(
                'visible',
                YesNoType::class,
                [
                    'label' => 'label.visible'
                ]
            )
            ->add('semestres', EntityType::class, array(
                'class'         => Semestre::class,
                'label'         => 'label.semestres_date',
                'choice_label'  => 'libelle',
                'query_builder' => function(SemestreRepository $semestreRepository) {
                    return $semestreRepository->findByDepartementBuilder($this->departement);
                },
                'required'      => true,
                'expanded'      => true,
                'multiple'      => true
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, static function(FormEvent $event) {
                $borne = $event->getData();
                $form = $event->getForm();
                $dateRange = $form->get('dateRange')->getData();
                $borne->setDateDebutPublication($dateRange['from_date']);
                $borne->setDateFinPublication($dateRange['to_date']);
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, static function(FormEvent $event) {
                $borne = $event->getData();
                $form = $event->getForm();
                $form->add('dateRange', DateRangeType::class, [
                    'label'     => 'dateRange',
                    'mapped'    => false,
                    'date_data' => [
                        'from' => $borne->getDateDebutPublication(),
                        'to'   => $borne->getDateFinPublication()
                    ]
                ]);
            });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => Borne::class,
            'departement'          => null,
            'translation_domain' => 'form'
        ]);
    }
}
