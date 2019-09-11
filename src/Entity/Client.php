<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


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
     * @Groups({"clients"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Renseigner le nom de l'envoyeur")
     * @Assert\Length(min="2",minMessage="La longueur du nom est de 2 lettres",max="15",maxMessage="La longueur du nom est de 15 lettres maximum")
     *  @Assert\Type(
     *     type="string",
     *     message="Le nom le est de type string.")
     */
    private $nomenvoyeur;

    /**
     * @Groups({"clients"})
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(message="Renseigner le prenom de l'envoyeur")
     * @Assert\Length(min="3",minMessage="La longueur minimum du prénom est de 3 lettres",max="30",maxMessage="La longueur du prenom est de 30 lettres maximum")
     *  @Assert\Type(
     *     type="string",
     *     message="Le prenom le est de type string.")
     */
    private $prenomenvoyeur;

    /**
     * @Groups({"clients"})
     * @ORM\Column(type="string")
     *  @Assert\NotBlank(message="Renseigner le téléphone de l'envoyeur")
     * @Assert\Length(min="9",minMessage="La longueur du téléphone est de 9",max="9",maxMessage="La longueur du téléphone est de 9 lettres maximum")
     */
    private $telephoneenvoyeur;

    /**
     * @Groups({"clients"})
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Renseigner le CNI de l'envoyeur")
     * @Assert\Length(min="13",minMessage="La longueur du CNI est de 13 chiffres",max="13",maxMessage="La longueur du CNI est de 13 chiffres maximum")
     */
    private $ncienvoyeur;

    /**
     * @Groups({"clients"})
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(message="Renseigner le nom du bénéficiaire")
     * @Assert\Length(min="2",minMessage="La longueur du nom est de 2 lettres",max="15",maxMessage="La longueur du nom est de 15 lettres maximum")
     *  @Assert\Type(
     *     type="string",
     *     message="Le nom le est de type string.")
     */
    private $nombeneficiaire;

    /**
     * @Groups({"clients"})
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(message="Renseigner le prenom du bénéficiaire")
     * @Assert\Length(min="3",minMessage="La longueur minimum du prénom est de 3 lettres",max="30",maxMessage="La longueur du prenom est de 30 lettres maximum")
     *  @Assert\Type(
     *     type="string",
     *     message="Le prenom le est de type string.")
     */
    private $prenombeneficiaire;

    /**
     * @Groups({"clients"})
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Renseigner le téléphone du beneficiaire")
     * @Assert\Length(min="9",minMessage="La longueur du téléphone est de 9",max="9",maxMessage="La longueur du téléphone est de 9 lettres maximum")
     */
    private $telephonebeneficiaire;

    /**
     * @Groups({"clients"})
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(min="13",minMessage="La longueur du CNI est de 13 chiffres",max="13",maxMessage="La longueur du CNI est de 13 chiffres maximum")
     */
    private $ncibeneficiaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="clients")
     */
    private $partanaire;

    /**
     * @Groups({"clients"})
     * @ORM\Column(type="string")
     */
    private $montant;

    
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

    public function setNcibeneficiaire(?int $ncibeneficiaire): self
    {
        $this->ncibeneficiaire = $ncibeneficiaire;

        return $this;
    }

    public function getPartanaire(): ?Partenaire
    {
        return $this->partanaire;
    }

    public function setPartanaire(?Partenaire $partanaire): self
    {
        $this->partanaire = $partanaire;

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

    
}
