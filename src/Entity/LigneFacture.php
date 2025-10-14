<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: 'App\\Repository\\LigneFactureRepository')]
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
    #[Assert\GreaterThanOrEqual(value: 0, message: 'La quantité doit être >= 0')]
    private ?int $quantite = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'Le prix unitaire doit être >= 0')]
    private ?string $prixUnitaire = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: 'La TVA doit être entre 0 et 100%')]
    private ?string $tva = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: 'La remise doit être entre 0 et 100%')]
    private ?string $remise = null;

    #[Assert\Callback]
    public function validateBusinessRules(\Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        // Pour une section: forcer quantite/prix/tva à 0
        if ($this->isSection) {
            if ((int)($this->quantite ?? 0) !== 0 || (float)($this->prixUnitaire ?? 0) != 0.0 || (float)($this->tva ?? 0) != 0.0) {
                $context->buildViolation('Pour une section, quantité, prix et TVA doivent être 0')
                    ->atPath('designation')
                    ->addViolation();
            }
            return;
        }
        // Pour une ligne normale: règles strictes
        if (($this->quantite ?? 0) < 1) {
            $context->buildViolation('La quantité doit être au moins 1')
                ->atPath('quantite')
                ->addViolation();
        }
        if (($this->prixUnitaire === null) || (float)$this->prixUnitaire <= 0) {
            $context->buildViolation('Le prix unitaire doit être > 0')
                ->atPath('prixUnitaire')
                ->addViolation();
        }
        if (($this->tva === null) || (float)$this->tva < 0 || (float)$this->tva > 100) {
            $context->buildViolation('La TVA doit être entre 0 et 100%')
                ->atPath('tva')
                ->addViolation();
        }
    }

    #[ORM\Column(type: 'boolean')]
    private bool $isSection = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $position = null;

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

    public function getMontantRemise(): float
    {
        $remise = (float)($this->remise ?? 0);
        return $this->getMontantHt() * ($remise / 100);
    }

    public function getMontantHtApresRemise(): float
    {
        return $this->getMontantHt() - $this->getMontantRemise();
    }

    public function getMontantTva(): float
    {
        return $this->getMontantHtApresRemise() * ((float)$this->tva / 100);
    }

    public function getMontantTtc(): float
    {
        return $this->getMontantHtApresRemise() + $this->getMontantTva();
    }

    public function getRemise(): ?string
    {
        return $this->remise;
    }

    public function setRemise(?string $remise): self
    {
        $this->remise = $remise;

        return $this;
    }

    public function isSection(): bool
    {
        return $this->isSection;
    }

    public function setIsSection(bool $isSection): self
    {
        $this->isSection = $isSection;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}

