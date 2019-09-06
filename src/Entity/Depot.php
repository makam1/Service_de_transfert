<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\DepotRepository")
 */
class Depot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"depots"})
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Renseignez le montant")
     * @Assert\Range(min="75000",minMessage="Le dépôt minimum est de {{ limit }}")
     * @Assert\Positive
     * @Assert\Type(
     *     type="integer",
     *     message="Le montant est de type integer.")
     */
    private $montant;

    /**
     * @Groups({"depots"})
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @Groups({"depots"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="depots")
     * @Assert\NotBlank(message="Renseignez le compte")
     * @ORM\JoinColumn(nullable=false)
     */
    private $compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}
