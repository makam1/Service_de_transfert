<?php
namespace App\Controller;
use App\Entity\Compte;
use App\Form\CompteType;
use App\Entity\Partenaire;
use App\Entity\Utilisateur;
use App\Form\PartenaireType;
use App\Form\UtilisateurType;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



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
    public function new( UserPasswordEncoderInterface $encoder,Request $request,UserPasswordEncoderInterface $passwordEncoder,SerializerInterface $serializer,EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);
        $data=$request->request->all();
        $form->submit($data);

        $compte = new Compte();
        $form1 = $this->createForm(CompteType::class, $compte);
        $form1->handleRequest($request);
        $form1->submit($data);
        $date=date("Y").date("m").date("d").date("H").date("i").date("s");
        $compte->setNumerocompte($date);
        $compte->setSolde(0);
        $compte->setPartenaire($partenaire);
       

        $username = new Utilisateur();
        $form2= $this->createForm(UtilisateurType::class, $username);
        $form2->handleRequest($request);
        $file=$request->files->all()['imageFile'];
        $form2->submit($data);
        $username->setStatut("actif");
        $username->setRoles(["ROLE_ADMIN"]);
        $hash = $encoder->encodePassword($username, $username->getPassword());
        $username->setPassword($hash);
        $username->setImageFile($file);
        $username->setUpdatedAt(new \DateTime);
        $username->setPartenaire($partenaire);
        $username->setCompte($compte);
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

        $entityManager->persist($compte);
        $entityManager->persist($partenaire);
        $entityManager->persist($username);

        $entityManager->flush();

        return new Response('Partenaire, compte et admin associé ajouté', Response::HTTP_CREATED);


    }


    /** @Route("/{id}", name="partenaire_show", methods={"GET"})
     */
    public function show(Partenaire $partenaire): Response
    {
        return $this->render('partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }


    /**
     * @Route("/{id}/bloquer", name="partenaire_edit", methods={"GET","POST"})
     */
    public function bloquer(Request $request, Partenaire $partenaire): Response
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