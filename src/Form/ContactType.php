<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Form/ContactType.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

namespace App\Form;

use App\Entity\Contact;
use App\Form\Type\CiviliteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civilite', CiviliteType::class, ['label' => 'label.civilite'])
            ->add('nom', TextType::class, ['label' => 'label.nom'])
            ->add('prenom', TextType::class, ['label' => 'label.prenom'])
            ->add('fonction', TextType::class, ['label' => 'label.fonction'])
            ->add('email', EmailType::class, ['label' => 'label.email', 'required' => false])
            ->add('telephone', TextType::class, ['label' => 'label.telephone', 'required' => false])
            ->add('portable', TextType::class, ['label' => 'label.portable', 'required' => false])
            ->add('fax', TextType::class, ['label' => 'label.fax', 'required' => false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'translation_domain' => 'form'
        ]);
    }
}
