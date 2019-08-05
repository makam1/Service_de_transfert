<?php
namespace App\Controller;
use App\Entity\Partenaire;
use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Form\PartenaireType;
use App\Form\CompteType;
use App\Repository\PartenaireRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Repository\CompteRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;



/**
 * @Route("/api/partenaire")
 */
class PartenaireController extends AbstractController
{
    /**
     * @Route("/", name="partenaire_index", methods={"GET"})
     */
    public function index(PartenaireRepository $partenaireRepository): Response
    {
        return $this->render('partenaire/index.html.twig', [
            'partenaires' => $partenaireRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new", name="partenaire_new", methods={"GET","POST"})
     */
    public function new(Request $request,UserPasswordEncoderInterface $passwordEncoder,SerializerInterface $serializer,EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {

        $values=json_decode($request->getContent());
        $partenaire = new Partenaire();
        $partenaire->setRaisonsociale($values->raisonsociale);
        $partenaire->setNinea($values->ninea);
        $partenaire->setAdresse($values->adresse);

        $compte = new Compte();
        $date=date("Y").date("m").date("d").date("H").date("i").date("s");
        $compte->setNumerocompte($date);
        $compte->setSolde(0);
        $compte->setPartenaire($partenaire);

       
        $username = new Utilisateur();

        $username->setNom($values->nom);
        $username->setUsername($values->username);
        $username->setEmail($values->email);
        $username->setTelephone($values->telephone);
        $username->setStatut("actif");
        $username->setRoles(["ROLE_ADMIN"]);
        $username->setPassword($passwordEncoder->encodePassword($username,$values->password));
        $username->setPartenaire($partenaire);

        $entityManager = $this->getDoctrine()->getManager();
            $errors = $validator->validate($partenaire);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type'=>  'application/json'
                ]);
            }
            $m = $validator->validate($username);
            if(count($m)) {
                $m = $serializer->serialize($m, 'json');
                return new Response($m, 500, [
                    'Content-Type'=>  'application/json'
                ]);
            }
        $entityManager->persist($partenaire);
        $entityManager->persist($compte);
        $entityManager->persist($username);
        $entityManager->flush();

        return new Response("Le partenaire, son compte et l'admin associé et ont été ajouté",Response::HTTP_CREATED);
    
    }


    /**!rtenaire_show", methods={"GET"})
     */
    public function show(Partenaire $partenaire): Response
    {
        return $this->render('partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="partenaire_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Partenaire $partenaire): Response
    {
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('partenaire_index');
        }
        return $this->render('partenaire/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form->createView(),
        ]);
    }
   
}