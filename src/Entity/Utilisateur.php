<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 *  @UniqueEntity(fields={"username"}, message="Cet utilisateur existe déjà")
 * @Vich\Uploadable
 */
class Utilisateur implements UserInterface
{
    /**
     * @Groups({"users"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *  @Groups({"users"})
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank(message="Renseigner le username")
     * @Assert\Length(min="5",minMessage="La longueur du username est de 5",max="10",maxMessage="La longueur du username est de 10")
    *   @Assert\Type(
     *     type="string",
     *     message="Le username le est de type string.")
     */
    private $username;
   

    /**
     * @Groups({"users"})
     * @ORM\Column(type="json")
     */
    
    private $roles = [];

    /**
     * @Groups({"users"})
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Renseignez le password")
     * 
     */
    private $password;

    /**
     * @Groups({"users"})
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Renseignez le nom")
     * @Assert\Length(min="2",minMessage="Le nom doit etre long 2 caractères minimum",max="10",maxMessage="Le mot de pase doit etre long 10 caractères maximum")
    *@Assert\Type(
     *     type="string",
     *     message="Le nom est de type string.")
     */
    private $nom;

    /**
     * @Groups({"users"})
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank(message="Renseignez l'email")
     * @Assert\Length(min="10",minMessage="L'email' doit etre long 10 caractères minimum",max="20",maxMessage="L'email' doit etre long 20 caractères maximum")
    *@Assert\Type(
     *     type="string",
     *     message="Donner un email valide")
     * @Assert\Email
     */
    private $email;

    /**
     * @Groups({"users"})
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Renseignez le téléphone")
     * @Assert\Length(min="9",minMessage="Le téléphone doit etre long 9 caractères minimum",max="9")
    *@Assert\Type(
     *     type="integer",
     *     message="Le télephone est de type integer")
     */
    private $telephone;

    /**
     * @Groups({"users"})
     * @ORM\Column(type="string", length=20)
     * 
     */
    private $statut;

    /**
     * @Groups({"users"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="utilisateurs")
     */
    private $partenaire;
       /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName")
     * 
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imageName;
      /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @Groups({"users"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="utilisateur")
     */
    private $compte;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operation", mappedBy="utilisateur")
     */
    private $operations;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }

  
    public function getId(): ?int
    {
        return $this->id;
    }
    

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

        /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
       // $roles[] = 'ROLE_ADMIN';
        return array_unique($roles);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }
  /** 
    * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
    */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
 
        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    /** 
     * Get the value of updatedAt
     *
     * @return  \DateTime
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /** 
     * Set the value of updatedAt
     *
     * @param  \DateTime  $updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

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

    /**
     * @return Collection|Operation[]
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations[] = $operation;
            $operation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->contains($operation)) {
            $this->operations->removeElement($operation);
            // set the owning side to null (unless already changed)
            if ($operation->getUtilisateur() === $this) {
                $operation->setUtilisateur(null);
            }
        }

        return $this;
    }


}
