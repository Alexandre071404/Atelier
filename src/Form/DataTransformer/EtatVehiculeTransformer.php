<?php

namespace App\Form\DataTransformer;

use App\Enum\EtatVehicule;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EtatVehiculeTransformer implements DataTransformerInterface
{
        //Cette classe permet la tranformation des données de EtatVehicule en string et inverse.
    public function transform($etatVehicule): string
    {
        if (null === $etatVehicule) {
            return '';
        }
        return "".$etatVehicule->value;
    }

    public function reverseTransform($etatString): ?EtatVehicule
    {
        if (!$etatString) {
            return null; 
        }

        try {
            return EtatVehicule::from($etatString); 
        } 
        catch (\ValueError $e) {
            throw new TransformationFailedException('Etat du véhicule est invalide.');
        }
    }
}
