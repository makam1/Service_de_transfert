<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\CommissionRepository")
 */
class Commission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $etat;

    /**
     * @ORM\Column(type="integer")
     */
    private $systeme;

    /**
     * @ORM\Column(type="integer")
     */
    private $partenaire;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Operation", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $operation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSysteme(): ?int
    {
        return $this->systeme;
    }

    public function setSysteme(int $systeme): self
    {
        $this->systeme = $systeme;

        return $this;
    }

    public function getPartenaire(): ?int
    {
        return $this->partenaire;
    }

    public function setPartenaire(int $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    public function getOperation(): ?Operation
    {
        return $this->operation;
    }

    public function setOperation(Operation $operation): self
    {
        $this->operation = $operation;

        return $this;
    }
}
