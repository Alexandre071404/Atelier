<?php

// src/Form/VehiculeType.php
namespace App\Form;

use App\Entity\Vehicule;
use App\Enum\EtatVehicule;
use App\Form\DataTransformer\EtatVehiculeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    private EtatVehiculeTransformer $etatVehiculeTransformer;

    public function __construct(EtatVehiculeTransformer $etatVehiculeTransformer)
    {
        $this->etatVehiculeTransformer = $etatVehiculeTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du véhicule',
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État du véhicule',
                'choices' => [
                    'Neuf' => EtatVehicule::NEUF->value,
                    'Endommagé' => EtatVehicule::ENDOMMAGE->value,
                    'Cassé' => EtatVehicule::CASSE->value,
                ],
            ])
            ->add('plaque', TextType::class, [
                'label' => 'Plaque d’immatriculation',
            ]);

        // Appliquer le transformer au champ 'etat'
        $builder->get('etat')->addModelTransformer($this->etatVehiculeTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}

