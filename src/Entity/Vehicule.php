<?php

namespace App\Entity;

use App\Enum\EtatVehicule; // on créé une énumération afin de pouvior choisir qu'entre trois États
use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', enumType: EtatVehicule::class)] 
    private EtatVehicule $etat; // On a trois choix NEUF/ENDOMMAGE/CASSE 

    #[ORM\Column(length: 255)]
    private ?string $plaque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    //on retourne un type EtatVehicule 
    
    public function getEtat(): EtatVehicule 
    {
        return $this->etat;
    }

    public function setEtat(EtatVehicule $etat): static 
    {
        $this->etat = $etat;

        return $this;
    }

    public function getPlaque(): ?string
    {
        return $this->plaque;
    }

    public function setPlaque(string $Plaque): static
    {
        $this->plaque = $Plaque;

        return $this;
    }
}
