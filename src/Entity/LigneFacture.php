<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\LigneFactureRepository')]
#[ORM\Table(name: 'ligne_facture')]
class LigneFacture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Facture::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $facture = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'La désignation est obligatoire')]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $designation = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'La quantité est obligatoire')]
    #[Assert\Positive(message: 'La quantité doit être positive')]
    private ?int $quantite = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix unitaire est obligatoire')]
    #[Assert\Positive(message: 'Le prix unitaire doit être positif')]
    private ?string $prixUnitaire = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\NotBlank(message: 'La TVA est obligatoire')]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: 'La TVA doit être entre 0 et 100%')]
    private ?string $tva = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): self
    {
        $this->facture = $facture;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(string $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getMontantHt(): float
    {
        return $this->quantite * (float)$this->prixUnitaire;
    }

    public function getMontantTva(): float
    {
        return $this->getMontantHt() * ((float)$this->tva / 100);
    }

    public function getMontantTtc(): float
    {
        return $this->getMontantHt() + $this->getMontantTva();
    }
}

