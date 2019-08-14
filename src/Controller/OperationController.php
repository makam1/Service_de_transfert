<?php

namespace App\Controller;

use App\Entity\Frais;
use App\Entity\Client;
use App\Form\ClientType;
use App\Entity\Operation;
use App\Entity\Commission;
use App\Form\OperationType;
use App\Form\CommissionType;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("api/operation")
 */
class OperationController extends AbstractController
{
    /**
     * @Route("/", name="operation_index", methods={"GET"})
     */
    public function index(OperationRepository $operationRepository): Response
    {
        return $this->render('operation/index.html.twig', [
            'operations' => $operationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/envoi", name="operation_envoi", methods={"GET","POST"})
     */
    public function envoi(Request $request,EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $user=$this->getUser();
        $id=$this->getUser()->getId();
        $part=$this->getUser()->getPartenaire()->getId();
  
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        $data=$request->request->all();
        $form->submit($data);

        $operation = new Operation();
        $form1 = $this->createForm(OperationType::class, $operation);
        $form1->handleRequest($request);
        $form1->submit($data);
        $operation->setDate(new \Datetime());
        $operation->setCode($id.$part.date("Y").date("m"));
        $operation->setUtilisateur($user);
        $operation->setClient($client);


        $compte= $user->getCompte();
        $compte->setSolde($compte->getSolde()+$operation->getMontant());
       
        $commission = new Commission();
        $form2= $this->createForm(CommissionType::class, $commission);
        $form2->handleRequest($request);
        $form2->submit($data);
        $commission->setEtat(($operation->getFrais()*40)/100);
        $commission->setSysteme(($operation->getFrais()*40)/100);
        $commission->setPartenaire(($operation->getFrais()*40)/100);
        $commission->setOperation($operation);
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($commission);
        $entityManager->persist($client);
        $entityManager->persist($operation);

        $entityManager->flush();

        return new Response('Envoi reussi le code est :', Response::HTTP_CREATED);
    }
    /**
     * @Route("/retait", name="operation_retrait", methods={"GET","POST"})
     */
    public function retrait(Request $request,EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        
    }

    /**
     * @Route("/{id}", name="operation_show", methods={"GET"})
     */
    public function show(Operation $operation): Response
    {
        return $this->render('operation/show.html.twig', [
            'operation' => $operation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="operation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Operation $operation): Response
    {
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('operation_index');
        }

        return $this->render('operation/edit.html.twig', [
            'operation' => $operation,
            'form' => $form->createView(),
        ]);
    }

}
