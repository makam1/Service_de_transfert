<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomenvoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomenvoyeur;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephoneenvoyeur;

    /**
     * @ORM\Column(type="integer")
     */
    private $ncienvoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombeneficiaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenombeneficiaire;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephonebeneficiaire;

    /**
     * @ORM\Column(type="integer")
     */
    private $ncibeneficiaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomenvoyeur(): ?string
    {
        return $this->nomenvoyeur;
    }

    public function setNomenvoyeur(string $nomenvoyeur): self
    {
        $this->nomenvoyeur = $nomenvoyeur;

        return $this;
    }

    public function getPrenomenvoyeur(): ?string
    {
        return $this->prenomenvoyeur;
    }

    public function setPrenomenvoyeur(string $prenomenvoyeur): self
    {
        $this->prenomenvoyeur = $prenomenvoyeur;

        return $this;
    }

    public function getTelephoneenvoyeur(): ?int
    {
        return $this->telephoneenvoyeur;
    }

    public function setTelephoneenvoyeur(int $telephoneenvoyeur): self
    {
        $this->telephoneenvoyeur = $telephoneenvoyeur;

        return $this;
    }

    public function getNcienvoyeur(): ?int
    {
        return $this->ncienvoyeur;
    }

    public function setNcienvoyeur(int $ncienvoyeur): self
    {
        $this->ncienvoyeur = $ncienvoyeur;

        return $this;
    }

    public function getNombeneficiaire(): ?string
    {
        return $this->nombeneficiaire;
    }

    public function setNombeneficiaire(string $nombeneficiaire): self
    {
        $this->nombeneficiaire = $nombeneficiaire;

        return $this;
    }

    public function getPrenombeneficiaire(): ?string
    {
        return $this->prenombeneficiaire;
    }

    public function setPrenombeneficiaire(string $prenombeneficiaire): self
    {
        $this->prenombeneficiaire = $prenombeneficiaire;

        return $this;
    }

    public function getTelephonebeneficiaire(): ?int
    {
        return $this->telephonebeneficiaire;
    }

    public function setTelephonebeneficiaire(int $telephonebeneficiaire): self
    {
        $this->telephonebeneficiaire = $telephonebeneficiaire;

        return $this;
    }

    public function getNcibeneficiaire(): ?int
    {
        return $this->ncibeneficiaire;
    }

    public function setNcibeneficiaire(int $ncibeneficiaire): self
    {
        $this->ncibeneficiaire = $ncibeneficiaire;

        return $this;
    }
}
