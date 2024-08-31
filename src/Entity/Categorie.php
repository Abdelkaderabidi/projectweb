<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CategorieRepository::class)]

class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idCat = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'ce champ est obligatoire')]
    private ?string $nomCat = null;


    public function getIdCat(): ?int
    {
        return $this->idCat;
    }

    public function getNomCat(): ?string
    {
        return $this->nomCat;
    }

    public function setNomCat(string $nomCat): static
    {
        $this->nomCat = $nomCat;

        return $this;
    }


}
