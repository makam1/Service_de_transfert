<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;




/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\PartenaireRepository")
 * @UniqueEntity(fields={"ninea"}, message="Ce ninea existe déjà")
 * @UniqueEntity(fields={"raisonsociale"}, message="Ce partenaire existe déjà")
 */
class Partenaire
{
    /**
     * @Groups({"partenaires"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"partenaires","users","comptes","depots"})
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide")
     * @Assert\Length(min="7",minMessage="La longueur minimale de la raison sociale est de 7",max="30",maxMessage="La longueur maximale la raison sociale est de 30")
    *@Assert\Type(
     *     type="string",
     *     message="La raison sociale est de type string.")
     */
    private $raisonsociale;

    /**
     * 
     * @Groups({"partenaires","depots"})
     * @ORM\Column(type="string", length=10, unique=true)
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide")
     * @Assert\Length(min="10",max="10",minMessage="La longueur du ninea est de 10")
     * @Assert\Type(
     *     type="string",
     *     message="Le ninea est de type string.")
     */
    private $ninea;

    /**
     * @Groups({"partenaires"})
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Ce champ ne doit pas être vide")
     * @Assert\Type(
     *     type="string",
     *     message="L'adresse est de type string.")
     */
    private $adresse;

    /**
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Utilisateur", mappedBy="partenaire")
     */
    private $utilisateurs;

    /**
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="partenaire")
     */
    private $comptes;

    /**
     *
     * @Groups({"partenaires"})
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

   

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Client", mappedBy="partanaire")
     */
    private $clients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commission", mappedBy="utilisateur")
     */
    private $commissions;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->comptes = new ArrayCollection();
        $this->commissions = new ArrayCollection();
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonsociale(): ?string
    {
        return $this->raisonsociale;
    }

    public function setRaisonsociale(string $raisonsociale): self
    {
        $this->raisonsociale = $raisonsociale;

        return $this;
    }

    public function getNinea(): ?string
    {
        return $this->ninea;
    }

    public function setNinea(string $ninea): self
    {
        $this->ninea = $ninea;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->setPartenaire($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->removeElement($utilisateur);
            // set the owning side to null (unless already changed)
            if ($utilisateur->getPartenaire() === $this) {
                $utilisateur->setPartenaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setPartenaire($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            // set the owning side to null (unless already changed)
            if ($compte->getPartenaire() === $this) {
                $compte->setPartenaire(null);
            }
        }

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setPartanaire($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            // set the owning side to null (unless already changed)
            if ($client->getPartanaire() === $this) {
                $client->setPartanaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commission[]
     */
    public function getCommissions(): Collection
    {
        return $this->commissions;
    }

    public function addCommission(Commission $commission): self
    {
        if (!$this->commissions->contains($commission)) {
            $this->commissions[] = $commission;
            $commission->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommission(Commission $commission): self
    {
        if ($this->commissions->contains($commission)) {
            $this->commissions->removeElement($commission);
            // set the owning side to null (unless already changed)
            if ($commission->getUtilisateur() === $this) {
                $commission->setUtilisateur(null);
            }
        }

        return $this;
    }
}
