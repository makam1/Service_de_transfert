<?php
namespace App\Controller;
use App\Entity\Depot;
use App\Form\DepotType;
use App\Entity\Compte;
use App\Repository\DepotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;



/**
 * @Route("/api/depot")
 */
class DepotController extends AbstractController
{
    /**
     * @Route("/", name="depot_index", methods={"GET"})
     */
    public function index(DepotRepository $depotRepository): Response
    {
        return $this->render('depot/index.html.twig', [
            'depots' => $depotRepository->findAll(),
        ]);
    }
    /**
     * @Route("/new", name="depot_new", methods={"GET","POST"})
     */
    public function new(Request $request,SerializerInterface $serializer,EntityManagerInterface $entityManager,ValidatorInterface $validator ): Response
    {
        $depot = new Depot();
      
        $form = $this->createForm(DepotType::class,$depot);
        $data=$request->request->all();
        $depot->setDate(new \Datetime());
       
        $form->submit($data);
        if($form->isSubmitted()){
            $compte= $depot->getCompte();
            $compte->setSolde($compte->getSolde()+$depot->getMontant());
            $entityManager = $this->getDoctrine()->getManager();
            $errors = $validator->validate($depot);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->persist($compte);
            $entityManager->persist($depot);
            $entityManager->flush();
                  
        return new Response('Le dépôt a été effectué',Response::HTTP_CREATED);
        }
        $data = [
            'status' => 500,
            'message' => 'Vous devez renseigner le montant et le compte où doit être effectuer le dépot '
        ];
        return new Response($data, 500);
    }
    /**
     * @Route("/{id}", name="depot_show", methods={"GET"})
     */
    public function show(Depot $depot): Response
    {
        return $this->render('depot/show.html.twig', [
            'depot' => $depot,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="depot_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Depot $depot): Response
    {
        $form = $this->createForm(DepotType::class, $depot);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->getDoctrine()->getManager()->flush();
            return new Response('Le dépôt a été effectué',Response::HTTP_CREATED);
        }
    }
}