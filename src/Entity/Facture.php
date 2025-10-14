<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: 'App\\Repository\\FactureRepository')]
#[ORM\Table(name: 'facture')]
class Facture
{
    public const ETAT_BROUILLON = 'Brouillon';
    public const ETAT_VALIDEE = 'Validée';
    public const ETAT_PAYEE = 'Payée';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private ?string $reference = null;

    #[ORM\ManyToOne(targetEntity: Tiers::class, inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le client est obligatoire')]
    private ?Tiers $client = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: 'La date de facture est obligatoire')]
    private ?\DateTimeInterface $dateFacture = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateEcheance = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(choices: [self::ETAT_BROUILLON, self::ETAT_VALIDEE, self::ETAT_PAYEE])]
    private string $etat = self::ETAT_BROUILLON;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalHt = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalTtc = '0.00';

    #[ORM\Column(type: 'string', length: 3)]
    private string $devise = 'MAD';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: LigneFacture::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[Assert\Valid]
    private Collection $lignes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->dateFacture = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getClient(): ?Tiers
    {
        return $this->client;
    }

    public function setClient(?Tiers $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDateFacture(): ?\DateTimeInterface
    {
        return $this->dateFacture;
    }

    public function setDateFacture(\DateTimeInterface $dateFacture): self
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(?\DateTimeInterface $dateEcheance): self
    {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    public function getEtat(): string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTotalHt(): string
    {
        return $this->totalHt;
    }

    public function setTotalHt(string $totalHt): self
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    public function getTotalTtc(): string
    {
        return $this->totalTtc;
    }

    public function setTotalTtc(string $totalTtc): self
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    public function getDevise(): string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return Collection<int, LigneFacture>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(LigneFacture $ligne): self
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setFacture($this);
        }

        return $this;
    }

    public function removeLigne(LigneFacture $ligne): self
    {
        if ($this->lignes->removeElement($ligne)) {
            // set the owning side to null (unless already changed)
            if ($ligne->getFacture() === $this) {
                $ligne->setFacture(null);
            }
        }

        return $this;
    }

    public function calculerTotaux(): void
    {
        $totalHt = 0;
        $totalTva = 0;

        foreach ($this->lignes as $ligne) {
            // ignorer les sections dans les totaux
            if (method_exists($ligne, 'isSection') && $ligne->isSection()) {
                continue;
            }
            $montantHt = $ligne->getQuantite() * $ligne->getPrixUnitaire();
            $montantTva = $montantHt * ($ligne->getTva() / 100);
            
            $totalHt += $montantHt;
            $totalTva += $montantTva;
        }

        $this->totalHt = number_format($totalHt, 2, '.', '');
        $this->totalTtc = number_format($totalHt + $totalTva, 2, '.', '');
    }

    public function getTotalTva(): string
    {
        return number_format((float)$this->totalTtc - (float)$this->totalHt, 2, '.', '');
    }

    public function getEtatsDisponibles(): array
    {
        return [
            self::ETAT_BROUILLON => self::ETAT_BROUILLON,
            self::ETAT_VALIDEE => self::ETAT_VALIDEE,
            self::ETAT_PAYEE => self::ETAT_PAYEE,
        ];
    }

    #[Assert\Callback]
    public function validateBusinessRules(ExecutionContextInterface $context): void
    {
        // date d'échéance > date de facture si renseignée
        if ($this->dateEcheance instanceof \DateTimeInterface && $this->dateFacture instanceof \DateTimeInterface) {
            if ($this->dateEcheance <= $this->dateFacture) {
                $context->buildViolation("La date d'échéance doit être postérieure à la date de facture.")
                    ->atPath('dateEcheance')
                    ->addViolation();
            }
        }

        // désignations uniques (insensible à la casse/espaces)
        $seenDesignations = [];
        foreach ($this->lignes as $index => $ligne) {
            $designation = trim((string) $ligne->getDesignation());
            if ($designation === '') {
                continue;
            }
            $key = mb_strtolower($designation);
            if (isset($seenDesignations[$key])) {
                $context->buildViolation('Chaque désignation de ligne doit être unique dans la facture.')
                    ->atPath('lignes['.$index.'].designation')
                    ->addViolation();
            } else {
                $seenDesignations[$key] = true;
            }
        }
    }
}

