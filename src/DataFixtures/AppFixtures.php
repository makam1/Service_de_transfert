<?php

namespace App\DataFixtures;

use App\Entity\Partenaire;
use App\Entity\Compte;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $actif='actif';
        $partenaire = new Partenaire();
        $partenaire->setRaisonsociale("système");
        $partenaire->setNinea("12349876AW");
        $partenaire->setAdresse("Mermoz");
        $partenaire->setStatut($actif);
        $manager->persist($partenaire);

        $compte= new Compte();
        $compte->setPartenaire($partenaire);
        $compte->setNumerocompte('Non alloué');
        $compte->setSolde(0);
        $manager->persist($compte);



        $admin = new Utilisateur();
        $admin->setNom("mairame");
        $admin->setUsername("makam12");
        $admin->setEmail("mak@gmail.com");
        $admin->setTelephone(778900987);
        $admin->setStatut($actif);
        $admin->setImageName("image.png");
        $admin->setUpdatedAt(new \DateTime());
        $admin->setRoles(["ROLE_SUPERADMIN"]);
        $admin->setPassword($this->passwordEncoder->encodePassword($admin,'passer123'));
        $admin->setPartenaire($partenaire);
        $admin->setCompte($compte);
        $manager->persist($admin);

        $caissier = new Utilisateur();
        $caissier->setNom("caissier");
        $caissier->setUsername("caissier");
        $caissier->setEmail("caissier@gmail.com");
        $caissier->setTelephone(778900987);
        $caissier->setStatut($actif);
        $caissier->setRoles(["ROLE_CAISSIER"]);
        $caissier->setImageName("image.png");
        $caissier->setUpdatedAt(new \DateTime());
        $caissier->setPassword($this->passwordEncoder->encodePassword($admin,'passer123'));
        $caissier->setPartenaire($partenaire);
        $caissier->setCompte($compte);
        $manager->persist($caissier);

        



        $manager->flush();
    }
}
