<?php
//L'énumération des états que peuvent prendre les véhicules
namespace App\Enum;

enum EtatVehicule: string
{
    case NEUF = 'neuf';
    case ENDOMMAGE = 'endommagé';
    case CASSE = 'cassé';
}
