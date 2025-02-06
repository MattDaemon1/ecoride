<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le modèle est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le modèle ne peut pas dépasser 50 caractères.')]
    private ?string $modele = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'L\'immatriculation est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'L\'immatriculation ne peut pas dépasser 50 caractères.')]
    #[Assert\Regex(
        pattern: '/^[A-Z]{2}-\d{3}-[A-Z]{2}$/',
        message: 'Le format de l\'immatriculation doit être comme "AB-123-CD".'
    )]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'L\'énergie est obligatoire.')]
    #[Assert\Choice(choices: ['Électrique', 'Essence', 'Diesel', 'Autre'], message: 'Choisissez une énergie valide.')]
    private ?string $energie = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La couleur est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'La couleur ne peut pas dépasser 50 caractères.')]
    private ?string $couleur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de première immatriculation est obligatoire.')]
    #[Assert\Type(\DateTimeInterface::class, message: 'La date de première immatriculation doit être valide.')]
    private ?\DateTimeInterface $datePremiereImmatriculation = null;

    #[ORM\ManyToOne(inversedBy: 'voitures')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')] // Supprime la voiture si l'utilisateur est supprimé
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'voiture')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'SET NULL')] // Si la marque est supprimée, garde la voiture avec "null"
    private ?Marque $marque = null;

    /**
     * @var Collection<int, Covoiturage>
     */
    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: 'voiture', cascade: ['remove'])]
    private Collection $covoiturages;

    public function __construct()
    {
        $this->covoiturages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getEnergie(): ?string
    {
        return $this->energie;
    }

    public function setEnergie(string $energie): static
    {
        $this->energie = $energie;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getDatePremiereImmatriculation(): ?\DateTimeInterface
    {
        return $this->datePremiereImmatriculation;
    }

    public function setDatePremiereImmatriculation(\DateTimeInterface $datePremiereImmatriculation): static
    {
        $this->datePremiereImmatriculation = $datePremiereImmatriculation;

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

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(Covoiturage $covoiturage): static
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturages->add($covoiturage);
            $covoiturage->setVoiture($this);
        }

        return $this;
    }

    public function removeCovoiturage(Covoiturage $covoiturage): static
    {
        if ($this->covoiturages->removeElement($covoiturage)) {
            if ($covoiturage->getVoiture() === $this) {
                $covoiturage->setVoiture(null);
            }
        }

        return $this;
    }

    /**
     * Permet d'afficher correctement l'entité dans un choix Symfony
     */
    public function __toString(): string
    {
        $marque = $this->marque ? (string) $this->marque : 'Marque inconnue';
        $modele = $this->modele ?? 'Modèle inconnu';
        $immatriculation = $this->immatriculation ?? 'Immatriculation inconnue';

        return sprintf('%s %s (%s)', $marque, $modele, $immatriculation);
    }
}
