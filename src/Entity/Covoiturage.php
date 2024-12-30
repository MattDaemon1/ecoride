<?php

namespace App\Entity;

use App\Repository\CovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)] // Stocke uniquement la date
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)] // Stocke uniquement l'heure
    private ?\DateTimeInterface $heureDepart = null;

    #[ORM\Column(length: 50)]
    private ?string $lieuDepart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)] // Stocke uniquement la date
    private ?\DateTimeInterface $dateArrivee = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)] // Stocke uniquement l'heure
    private ?\DateTimeInterface $heureArrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $lieuArrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $nbPlace = null;

    #[ORM\Column] // Stocke le prix en crédits
    private ?int $prixPersonne = null;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    private ?User $user = null;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'covoiturage')]
    private Collection $avis;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    private ?Voiture $voiture = null;

    public function __construct()
    {
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeInterface $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(\DateTimeInterface $heureDepart): static
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieuDepart;
    }

    public function setLieuDepart(string $lieuDepart): static
    {
        $this->lieuDepart = $lieuDepart;

        return $this;
    }

    public function getDateArrivee(): ?\DateTimeInterface
    {
        return $this->dateArrivee;
    }

    public function setDateArrivee(\DateTimeInterface $dateArrivee): static
    {
        $this->dateArrivee = $dateArrivee;

        return $this;
    }

    public function getHeureArrivee(): ?\DateTimeInterface
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(\DateTimeInterface $heureArrivee): static
    {
        $this->heureArrivee = $heureArrivee;

        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieuArrivee;
    }

    public function setLieuArrivee(string $lieuArrivee): static
    {
        $this->lieuArrivee = $lieuArrivee;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): static
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getPrixPersonne(): ?int
    {
        return $this->prixPersonne;
    }

    public function setPrixPersonne(int $prixPersonne): static
    {
        $this->prixPersonne = $prixPersonne;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setCovoiturage($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getCovoiturage() === $this) {
                $avi->setCovoiturage(null);
            }
        }

        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): static
    {
        $this->voiture = $voiture;

        return $this;
    }
}