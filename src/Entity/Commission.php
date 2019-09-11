<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


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
     *  @Groups({"coms"})
     * @ORM\Column(type="integer")
     */
    private $etat;

    /**
     * @Groups({"coms"})
     * @ORM\Column(type="integer")
     */
    private $systeme;

    /**
     * @Groups({"coms"})
     * @ORM\Column(type="integer")
     */
    private $partenaire;

    /**
     * @Groups({"coms"})
     * @ORM\OneToOne(targetEntity="App\Entity\Operation", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $operation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="commissions")
     */
    private $utilisateur;

   
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

    public function getUtilisateur(): ?Partenaire
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Partenaire $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

}
