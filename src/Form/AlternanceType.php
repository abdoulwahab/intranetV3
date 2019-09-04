<?php
/**
 * Copyright (C) 9 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
 * @file /Users/davidannebicque/htdocs/intranetv3/src/Form/AlternanceType.php
 * @author     David Annebicque
 * @project intranetv3
 * @date 04/09/2019 14:43
 * @lastUpdate 04/09/2019 14:42
 */

namespace App\Form;

use App\Entity\Alternance;
use App\Entity\Personnel;
use App\Form\Type\DateRangeType;
use App\Repository\PersonnelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlternanceType extends AbstractType
{
    private $departement;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->departement = $options['departement'];

        $builder
            ->add('typeContrat', ChoiceType::class, [
                'choices'  => [
                    Alternance::ALTERNANCE_APPRENTISSAGE       => Alternance::ALTERNANCE_APPRENTISSAGE,
                    Alternance::ALTERNANCE_PROFESSIONALISATION => Alternance::ALTERNANCE_PROFESSIONALISATION
                ],
                'expanded' => true,
                'label'    => 'label.contrat_alternance'
            ])
            ->add('etat', ChoiceType::class, [
                'choices'  => [
                    Alternance::ALTERNANCE_ETAT_INITIALISE => Alternance::ALTERNANCE_ETAT_INITIALISE,
                    Alternance::ALTERNANCE_ETAT_COMPLETE   => Alternance::ALTERNANCE_ETAT_COMPLETE,
                    Alternance::ALTERNANCE_ETAT_VALIDE     => Alternance::ALTERNANCE_ETAT_VALIDE
                ],
                'expanded' => true,
                'label'    => 'label.etat_alternance'
            ])
            ->add('dateRange', DateRangeType::class,
                ['label' => 'dateRange.periode.alternace', 'mapped' => false, 'required' => true])
            ->add('entreprise', EntrepriseType::class, ['label' => 'label.entreprise'])
            ->add('tuteur', ContactType::class, ['label' => 'label.tuteur'])
            ->add('tuteurUniversitaire', EntityType::class, [
                'label'         => 'label.tuteur_universitaire',
                'expanded'      => false,
                'multiple'      => false,
                'class'         => Personnel::class,
                'help'          => 'help.tuteur_universitaire',
                'choice_label'  => 'display',
                'query_builder' => function(PersonnelRepository $personnelRepository) {
                    return $personnelRepository->findByDepartementBuilder($this->departement);
                },
                'attr'          => ['data-live-search' => 'true', 'data-provide' => 'selectpicker'],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, static function(FormEvent $event) {
                $alternance = $event->getData();
                $form = $event->getForm();
                $dateRange = $form->get('dateRange')->getData();
                $alternance->setDateDebut($dateRange['from_date']);
                $alternance->setDateFin($dateRange['to_date']);
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, static function(FormEvent $event) {
                $alternance = $event->getData();
                $form = $event->getForm();
                $form->add('dateRange', DateRangeType::class, [
                    'label'     => 'dateRange.periode',
                    'mapped'    => false,
                    'date_data' => ['from' => $alternance->getDateDebut(), 'to' => $alternance->getDateFin()]
                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => Alternance::class,
            'translation_domain' => 'form',
            'departement'          => null
        ]);
    }
}
