<?php
namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



/**
 * @Route("/api")
 */
class UtilisateurController extends AbstractController
{
   /**
     * @Route("/utilisateur", name="utilisateur_liste", methods={"GET"})
     *  
     */
    public function index(UtilisateurRepository $user,SerializerInterface $serializer): Response
    {
        $part=$user->findAll();
        $data = $serializer->serialize($part, 'json',['groups' => ['users']]);
        return new Response($data, 200, [
            'Content-Type'=>  'application/json'
        ]);
        
    }
        /**
         *@Route("/creer", name="creer", methods={"POST"})
        */
    
        public function creer(Request $request,UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $entityManager, ValidatorInterface $validator,SerializerInterface $serializer){
    
            $utilisateur = new Utilisateur();

            $id=$this->getUser()->getPartenaire();
    
            $form = $this->createForm(UtilisateurType::class, $utilisateur);
            $form->handleRequest($request);
            $data=$request->request->all();
            $file=$request->files->all()['imageFile'];
            
            $form->submit($data);
    
            $utilisateur->setRoles(["ROLE_SUPERADMIN"]);
    
            $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($hash);
            $utilisateur->setStatut("actif");
            $utilisateur->setPartenaire($id);
            $utilisateur->setImageFile($file);
            $utilisateur->setUpdatedAt(new \DateTime); 
            $entityManager = $this->getDoctrine()->getManager();
            $errors = $validator->validate($utilisateur);
                if(count($errors)) {
                    $errors = $serializer->serialize($errors, 'json');
                    return new Response($errors, 500, [
                        'Content-Type' => 'application/json'
                    ]);
                }
            $entityManager->persist($utilisateur);
            $entityManager->flush();
    
            return new Response('Super admin ajouté', Response::HTTP_CREATED);


    }
     
    /**
     * @Route("/admin", name="admin", methods={"GET","POST"})
     * 
     */
    public function admin(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer,ValidatorInterface $validator): Response
    {

       
        $utilisateur = new Utilisateur();

        $id=$this->getUser()->getPartenaire();

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        $data=$request->request->all();
        $file=$request->files->all()['imageFile'];
        
        $form->submit($data);

        $utilisateur->setRoles(["ROLE_ADMIN"]);

        $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($hash);
        $utilisateur->setStatut("actif");
        $utilisateur->setPartenaire($id);
        $utilisateur->setImageFile($file);
        $utilisateur->setUpdatedAt(new \DateTime); 
        $entityManager = $this->getDoctrine()->getManager();
        $errors = $validator->validate($utilisateur);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        return new Response('Administrateur ajouté', Response::HTTP_CREATED);
    }
    /**
     * @Route("/user", name="user", methods={"POST"})
     */
    public function user(Request $request,  EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator,SerializerInterface $serializer): Response
    {
        
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        $data=$request->request->all();
        $file=$request->files->all()['imageFile'];

        $form->submit($data);
        
        $id=$this->getUser()->getPartenaire();

        $utilisateur->setRoles(["ROLE_USER"]);
        $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($hash);
        $utilisateur->setImageFile($file);
        $utilisateur->setUpdatedAt(new \DateTime);
        $utilisateur->setStatut("actif");
        $utilisateur->setPartenaire($id);
        $entityManager = $this->getDoctrine()->getManager();
        $errors = $validator->validate($utilisateur);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }

        $entityManager->persist($utilisateur);

        $entityManager->flush();
        
        return new Response('Utilisateur ajouté', Response::HTTP_CREATED);
        
    }

     /**
     * @Route("/caissier", name="caissier", methods={"POST"})
     */
    public function caissier(Request $request,  EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer,ValidatorInterface $validator): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        $id=$this->getUser()->getPartenaire();

        $data=$request->request->all();
        $file=$request->files->all()['imageFile'];
        $form->submit($data);
        $utilisateur->setRoles(["ROLE_CAISSIER"]);
        $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($hash);
        $utilisateur->setStatut("actif");
        $utilisateur->setPartenaire($id);
        $utilisateur->setImageFile($file);
        $utilisateur->setUpdatedAt(new \DateTime);
        $entityManager = $this->getDoctrine()->getManager();
        $errors = $validator->validate($utilisateur);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }

        $entityManager->persist($utilisateur);

        $entityManager->flush();

        return new Response('Caissier ajouté', Response::HTTP_CREATED);
    }
    /**
     * @Route("/{id}", name="utilisateur_show", methods={"GET"})
     */
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
    /**
     * @Route("/{id}/bloquer", name="utilisateur_bloquer", methods={"GET","POST"})
     */
    public function bloquer(Request $request, Utilisateur $utilisateur): Response
    {
        if($utilisateur->getUsername()=='makam12'){
            return new Response('Vous ne pouvez pas bloquer le super admin', Response::HTTP_CREATED);
        }
        if($utilisateur->getStatut()=='actif'){
        $utilisateur->setStatut('bloqué');
        $this->getDoctrine()->getManager()->flush();
        return new Response('Utilisateur bloqué', Response::HTTP_CREATED);
        } else{
            $utilisateur->setStatut('actif');
        $this->getDoctrine()->getManager()->flush();
        return new Response('Utilisateur débloqué', Response::HTTP_CREATED);
        }  
    }
    
    /**
     * @Route("/{id}/user/compte", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function compte(Request $request, Utilisateur $utilisateur): Response
    {
        $compte=$utilisateur->getPartenaire()->getComptes()[0];
        $utilisateur->setCompte($compte);
        $this->getDoctrine()->getManager()->flush();
        return new Response('Le compte a été affecté à l\'utilisateur', Response::HTTP_CREATED);
    }
}