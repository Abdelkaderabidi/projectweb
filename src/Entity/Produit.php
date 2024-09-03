<?php

namespace App\Entity;



use App\Repository\ProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Categorie;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $refProd = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'ce champ est obligatoire')]
    private ?string $nomProd = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "La quantité doit être comprise entre {{ min }} et {{ max }}."
    )]
    private ?int $quantite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'ce champ est obligatoire')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: "Le prix doit être un nombre positif avec jusqu'à deux décimales."
    )]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"veuillez saisir l'image de la produit")]
    private ?string $image = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(name: "id_cat", referencedColumnName: "id_cat")]
    private ?Categorie $Categories = null;

    #[ORM\Column(nullable: true)]
    private ?bool $enPromo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThan("now", message: "La date de début de promo ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $dateDebutPromo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThan("now", message: "La date fin de promo ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $dateFinPromo = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixApresPromo = null;

    #[ORM\Column(nullable: true)]
    private ?float $fav = null;

    public function getRefProd(): ?int
    {
        return $this->refProd;
    }

    public function getNomProd(): ?string
    {
        return $this->nomProd;
    }

    public function setNomProd(string $nomProd): static
    {
        $this->nomProd = $nomProd;
        return $this;
    }

    public function getFav(): ?float
    {
        return $this->fav;
    }

    public function setFav(?float $fav): static
    {
        $this->fav = $fav;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getCategories(): ?Categorie
    {
        return $this->Categories;
    }

    public function setCategories(?Categorie $Categories): static
    {
        $this->Categories = $Categories;
        return $this;
    }

    public function isEnPromo(): ?bool
    {
        return $this->enPromo;
    }

    public function setEnPromo(?bool $enPromo): static
    {
        $this->enPromo = $enPromo;
        return $this;
    }

    public function getDateDebutPromo(): ?\DateTimeInterface
    {
        return $this->dateDebutPromo;
    }

    public function setDateDebutPromo(?\DateTimeInterface $dateDebutPromo): static
    {
        $this->dateDebutPromo = $dateDebutPromo;
        return $this;
    }

    public function getDateFinPromo(): ?\DateTimeInterface
    {
        return $this->dateFinPromo;
    }

    public function setDateFinPromo(?\DateTimeInterface $dateFinPromo): static
    {
        $this->dateFinPromo = $dateFinPromo;
        return $this;
    }

    public function getPrixApresPromo(): ?float
    {
        return $this->prixApresPromo;
    }

    public function setPrixApresPromo(?float $prixApresPromo): static
    {
        $this->prixApresPromo = $prixApresPromo;
        return $this;
    }
}
