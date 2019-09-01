<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 */
class Operation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *  @Groups({"listes"})
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @Groups({"listes"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    /**
     * @Groups({"listes"})
     * @ORM\Column(type="bigint")
     */
    private $code;

    /**
     * @Groups({"listes"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="operations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;

    /** 
     * @Groups({"listes"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Type", inversedBy="operations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @Groups({"listes"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @Groups({"listes"})
     * @ORM\Column(type="integer")
     */
    private $frais;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }
}
