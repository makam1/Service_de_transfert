<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Partenaire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;



/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{
    /**
     *@Route("/creer", name="creer", methods={"POST"})
     */
  public function creer(Request $request,UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $entityManager, ValidatorInterface $validator,SerializerInterface $serializer){
    $values=json_decode($request->getContent());
    if(isset($values->username)){

        $username = new Utilisateur();

        $username->setNom($values->nom);
        $username->setUsername($values->username);
        $username->setEmail($values->email);
        $username->setTelephone($values->telephone);
        $username->setStatut('actif');
        $username->setRoles(["ROLE_SUPERADMIN"]);
        $username->setPassword($passwordEncoder->encodePassword($username,$values->password));
        $errors = $validator->validate($username);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($username);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'Super-admin ajouté'
        ];
        return new JsonResponse($data, 201);
    }
    $data = [
        'status' => 500,
        'message' => 'Vous devez renseigner les informations de l\'admin'
    ];
    return new JsonResponse($data, 500);


    }
     
   /**
     * @Route("/connexion", name="connexion", methods={"POST"})
     * @return JsonResponse
     */
    public function connexion():JsonResponse
    {    
        $username =$this->getUser();
        $statut=$this->getUser()->getStatut();
        
        return $this->json([
            'username' => $username->getUsername(),
            'roles' => $username->getRoles(),
            'statut' => $username->getStatut(),
        ]);
        
    }
    
  }
