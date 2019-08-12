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
     * @Route("/admin", name="admin", methods={"GET","POST"})
     * 
     */
    public function admin(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder,SerializerInterface $serializer,ValidatorInterface $validator): Response
    {

       
        $utilisateur = new Utilisateur();


        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        $data=$request->request->all();
        $file=$request->files->all()['imageFile'];
        
        $form->submit($data);

        $utilisateur->setRoles(["ROLE_ADMIN"]);

        $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($hash);
        $utilisateur->setStatut("actif");
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
        $data=$request->request->all();
        $file=$request->files->all()['imageFile'];
        $form->submit($data);
        $utilisateur->setRoles(["ROLE_CAISSIER"]);
        $hash = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($hash);
        $utilisateur->setStatut("actif");
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
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $data = json_decode($request->getContent(), true);
        $form->handleRequest($request);
        $form->Submit($data);
        $this->getDoctrine()->getManager()->flush();
        return new Response('Modification effectif ', Response::HTTP_CREATED);
    }
}