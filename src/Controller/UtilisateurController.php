<?php
namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Client;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Form\RecDepotType;
use App\Form\UsernameType;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\ClientRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $id=$this->getUser()->getPartenaire()->getId();
        if($this->getUser()->getUsername()=='makam12'){
        $query = $this->getDoctrine()->getEntityManager()->createQuery('SELECT u FROM App:Utilisateur u WHERE u.roles NOT LIKE :role')->setParameter('role','%"ROLE_USER"%');
        $part = $query->getResult();   
        $data = $serializer->serialize($part, 'json',['groups' => ['users']]);
        return new Response($data, 200, [
            'Content-Type'=>  'application/json'
        ]);
          
        }else{
        $part3=$this->getDoctrine()->getRepository(Utilisateur::class)->findBy(array('partenaire'=>$id));
        $data3 = $serializer->serialize($part3, 'json',['groups' => ['users']]);
        return new Response($data3, 200, [
            'Content-Type'=>  'application/json'
        ]);
        }
        
    }
        /**
         *@Route("/creer", name="creer", methods={"POST"})
        */
    
        public function creer(Request $request,UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $entityManager, ValidatorInterface $validator,SerializerInterface $serializer){
    
            $utilisateur = new Utilisateur();
            $compte=$this->getUser()->getCompte();
            $id=$this->getUser()->getPartenaire();
    
            $form = $this->createForm(UtilisateurType::class, $utilisateur);
            $form->handleRequest($request);
            $data=$request->request->all();
            $file=$request->files->all()['imageFile'];
            
            $form->submit($data);
    
            $utilisateur->setRoles(["ROLE_SUPERADMIN"]);
    
            $hash = $passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($hash);
            $utilisateur->setStatut("actif");
            $utilisateur->setPartenaire($id);
            $utilisateur->setCompte($compte);
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
    
            return new JsonResponse('Super admin ajouté',200, [
                'Content-Type'=>  'application/json'
            ]);

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
        $compte=$this->getDoctrine()->getRepository(Compte::class)->findBy(array('numerocompte'=>'Non alloué'));
        $utilisateur->setRoles(["ROLE_ADMIN"]);

        $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($hash);
        $utilisateur->setStatut("actif");
        $utilisateur->setPartenaire($id);
        $utilisateur->setCompte($compte[0]);
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

        return new JsonResponse('Administrateur ajouté',200, [
            'Content-Type'=>  'application/json'
        ]);
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
        $compte=$this->getDoctrine()->getRepository(Compte::class)->findBy(array('numerocompte'=>'Non alloué'));

        $utilisateur->setRoles(["ROLE_USER"]);
        $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($hash);
        $utilisateur->setImageFile($file);
        $utilisateur->setUpdatedAt(new \DateTime);
        $utilisateur->setStatut("actif");
        $utilisateur->setPartenaire($id);
        $utilisateur->setCompte($compte[0]);
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
        
        return new JsonResponse('Utilisateur ajouté',200, [
            'Content-Type'=>  'application/json'
        ]);
        
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
        $compte=$this->getUser()->getCompte();

        $data=$request->request->all();
        $file=$request->files->all()['imageFile'];
        $form->submit($data);
        $utilisateur->setRoles(["ROLE_CAISSIER"]);
        $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($hash);
        $utilisateur->setStatut("actif");
        $utilisateur->setPartenaire($id);
        $utilisateur->setCompte($compte);
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
 
        return new JsonResponse('Caissier ajouté',200, [
            'Content-Type'=>  'application/json'
        ]);
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
            return new JsonResponse('Vous ne pouvez pas bloquer le super admin',500, [
                'Content-Type'=>  'application/json'
            ]);
        }
        if($utilisateur->getStatut()=='actif'){
        $utilisateur->setStatut('bloqué');
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse('Utilisateur bloqué',200, [
            'Content-Type'=>  'application/json'
        ]);
        } else{
            $utilisateur->setStatut('actif');
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse('Utilisateur débloqué',200, [
            'Content-Type'=>  'application/json'
        ]);
        }  
    }
    
    /**
    * @Route("/user/compte", name="utilisateur_edit", methods={"GET","POST"})
    */

    public function compte(Request $request): Response
    {

        $depot = new Depot();
        $form = $this->createForm(RecDepotType::class, $depot);
        $data=$request->request->all();
        $form->handleRequest($request);
        $form->submit($data);
        
        $num=$this->getDoctrine()->getRepository(Compte::class)->findOneBy(array('numerocompte'=>$depot->getNumerocompte()));
        
        $user = new Utilisateur();
        $form= $this->createForm(UsernameType::class, $user);
        $data=$request->request->all();
        $form->handleRequest($request);

        $username=$this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(array('username'=>$data));

        $username->setCompte($num);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse('Le compte a été affecté à l\'utilisateur',200, [
            'Content-Type'=>  'application/json'
        ]);
    }

     /**
     * @Route("/find/utilisateur", name="util_compte", methods={"GET","POST"})
     */
    public function retrouver(Request $request,SerializerInterface $serializer): Response
    {
        $user = new Utilisateur();
        $form= $this->createForm(UsernameType::class, $user);
        $data=$request->request->all();
        $form->handleRequest($request);

        $part=$this->getUser()->getPartenaire()->getId();

        $num=$this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(array('username'=>$data,'partenaire'=>$part));

        if($num==null){
            return new Response('Cet utilisateur n\'existe pas', 500, [
                'Content-Type'=>  'application/json'
                ]);
        }else{  
            $res = $serializer->serialize($num, 'json',['groups' => ['users']]);
            return new Response($res, 200, [
            'Content-Type'=>  'application/json'
            ]);
        }
    }
}