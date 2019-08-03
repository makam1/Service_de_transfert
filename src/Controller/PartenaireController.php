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
    public function new(Request $request,UserPasswordEncoderInterface $passwordEncoder,SerializerInterface $serializer,EntityManagerInterface $entityManager, ValidatorInterface $validator ): Response
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

        // relates this product to the category
        $compte->setPartenaire($partenaire);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($partenaire);
        $entityManager->persist($compte);
        $entityManager->flush();



        // return new JsonResponse($data, 500);

        // $compte = new Compte();
        // $date=date("Y").date("m").date("d").date("H").date("i").date("s");
        // $compte->setNumerocompte($date);
        // $compte->setSolde(0);
        // $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($compte);
        // $entityManager->flush();
    //     $partenaire = new Partenaire();
    //     $form = $this->createForm(PartenaireType::class, $partenaire);
    //     $data=json_decode($request->getContent(),true);
    //     $form->submit($data);
    //     if($form->isSubmitted()){
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $errors = $validator->validate($partenaire);
    //         if(count($errors)) {
    //             $errors = $serializer->serialize($errors, 'json');
    //             return new Response($errors, 500, [
    //                 'Content-Type' => 'application/json'
    //             ]);
    //         }
    //         $entityManager->persist($partenaire);
    //         $entityManager->flush();

        
    //          $idpartenaire = $this->getDoctrine()->getRepository(Compte::class)->findAll();
    //         foreach ($idpartenaire as $key => $value) {
    //            $id=$value->getId();
    //         }
            



        return new Response('Le partenaire et son compte ont été ajouté',Response::HTTP_CREATED);
    
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


    /**
     * @Route("/{id}", name="partenaire_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Partenaire $partenaire): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partenaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($partenaire);
            $entityManager->flush();
        }
        return $this->redirectToRoute('partenaire_index');
    }
}