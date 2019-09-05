<?php
namespace App\Controller;

use App\Entity\Partenaire;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{
    
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
    $this->passwordEncoder = $passwordEncoder;
    }
        /**
     *@Route("/login_check", name="connexion", methods={"POST"})
     * @return JsonResponse
     * @param Request $request
     * @param JWTEncoderInterface $JWTEncoder
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */

    public function login_check(Request $request, JWTEncoderInterface $JWTEncoder)
    {
        $user = $this->getUser();
       
        $isValid =$this->passwordEncoder;
        if (!$isValid) {
            return new JsonResponse('Votre username ou votre mot de passe est incorrect, veuillez saisir à nouveau');
        }

        $statut=$user->getStatut();
        $partenaire=$user->getPartenaire()->getStatut();

        if ($statut !='actif' || $partenaire!='actif') {
            return new JsonResponse('Vous êtes bloqué(e) veuillez contacter votre administrateur');
        
        }
        
        $token = $JWTEncoder->encode([
                'roles'=>$user->getRoles(),
                'id'=>$user->getId(),
                'username' => $user->getUsername(),
                'exp' => time() + 36000 // 1 hour expiration
            ]);

        return new JsonResponse(['token' => $token]);
    }
    
  }
