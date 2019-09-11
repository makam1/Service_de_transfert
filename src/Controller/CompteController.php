<?php
namespace App\Controller;
use App\Entity\Compte;
use App\Form\CompteType;
use App\Entity\Partenaire;
use App\Repository\CompteRepository;
use App\Form\RecherchePartenaireType;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/api/compte")
 */
class CompteController extends AbstractController
{
    /**
     * @Route("/liste", name="compte_liste", methods={"GET"})
     *  
     */
    public function liste(CompteRepository $compte,SerializerInterface $serializer): Response
    {
        $id=$this->getUser()->getPartenaire()->getId();
        if($this->getUser()->getPartenaire()->getRaisonsociale()=='système'){
        $compte=$this->getDoctrine()->getRepository(Compte::class)->findAll();
        }else{
        $compte=$this->getDoctrine()->getRepository(Compte::class)->findBy(array('partenaire'=>$id));
        }
        $data = $serializer->serialize($compte, 'json',['groups' => ['comptes']]);
        return new Response($data, 200, [
            'Content-Type'=>  'application/json'
        ]);
        
    }
    /**
     * @Route("/new", name="comptenew", methods={"GET","POST"})
     */
    public function new(Request $request,SerializerInterface $serializer,EntityManagerInterface $entityManager, ValidatorInterface $validator ): Response
    {

        $compte = new Compte();
        $part= new Partenaire();
        $form = $this->createForm(RecherchePartenaireType::class,$part);
        $data=$request->request->all();
        $form->submit($data);
        $partenaire=$this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(array('raisonsociale'=>$part->getRaisonsociale()));
        if($form->isSubmitted()){
            $date=date("Y").date("m").date("d").date("H").date("i").date("s");
            $compte->setNumerocompte($date);
            $compte->setPartenaire($partenaire);
            $compte->setSolde(5000);
            $entityManager = $this->getDoctrine()->getManager();
            $errors = $validator->validate($compte);
            if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
            $entityManager->persist($compte);
            $entityManager->flush();
        
        return new JsonResponse('Le compte a été ajouté', 200, [
            'Content-Type'=>  'application/json'
            ]);
    }
       
        return new JsonResponse('Vous devez renseigner les informations du compte ', 500, [
            'Content-Type'=>  'application/json'
            ]);
    }
    /**
     * @Route("/{id}", name="compte_show", methods={"GET"})
     */
    public function show(Compte $compte): Response
    {
        return $this->render('compte/show.html.twig', [
            'compte' => $compte,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="compte_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Compte $compte): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('compte_index');
        }
        return $this->render('compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form->createView(),
        ]);
    }
   
}