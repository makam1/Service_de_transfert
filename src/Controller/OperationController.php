<?php

namespace App\Controller;

use App\Entity\Type;
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
        $compte= $user->getCompte();
        
        $type=$this->getDoctrine()->getRepository(Type::class)->findOneBy(array('libelle'=>'envoi'));


        if($compte->getSolde()>=10000){
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $client->setNcibeneficiaire(1) ;
        $form->handleRequest($request);
        $data=$request->request->all();
        $form->submit($data);

       
       
        $operation = new Operation();
        $form1 = $this->createForm(OperationType::class, $operation);
        $form1->handleRequest($request);
        $form1->submit($data);
        $montant=$operation->getMontant();

        if($montant<=$compte->getSolde() && $montant>500){

        $frais=$this->getDoctrine()->getRepository(Frais::class)->findAll();
        $f=0;
        
        foreach ($frais as $key => $value) {
            if($montant>=$value->getDe()){
                $f=$value->getFrais();
            }         
        }
        $op=$this->getDoctrine()->getRepository(Operation::class)->findAll();
        $i=0;  
        foreach ($op as $key => $value) {
            
                $i=$value->getId()+1;
                
        }

        $operation->setDate(new \Datetime());
        $code=$id.$part.$i.date("Y").date("m");
        $operation->setCode($code);
        $operation->setFrais($f);
        $operation->setUtilisateur($user);
        $operation->setClient($client);
        $operation->setType($type);

        $compte->setSolde($compte->getSolde()-$operation->getMontant());
       
        $commission = new Commission();
        $form2= $this->createForm(CommissionType::class, $commission);
        $form2->handleRequest($request);
        $form2->submit($data);
        $commission->setEtat(($f*40)/100);
        $commission->setSysteme(($f*30)/100);
        $commission->setPartenaire(($f*20)/100);
        $commission->setOperation($operation);
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($commission);
        $entityManager->persist($client);
        $entityManager->persist($operation);

        $entityManager->flush();
        }else{
            return new Response('Vous ne pouvez pas effectuer cet envoi', Response::HTTP_CREATED);

        }

        return new Response('Envoi reussi le code est :'.$code, Response::HTTP_CREATED);
        }else{
            return new Response('Vous ne pouvez pas faire d\'envoi le solde de votre compte est bas', Response::HTTP_CREATED);

        }
    }

    /**
     * @Route("/retrait", name="operation_retrait", methods={"GET","POST"})
     */
    public function retrait(Request $request,EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $id=$this->getUser();
        $type=$this->getDoctrine()->getRepository(Type::class)->findOneBy(array('libelle'=>'retrait'));

        $compte= $this->getUser()->getCompte();
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $data=$request->request->all();
        $form->handleRequest($request);
        $form->submit($data);
        $op=$this->getDoctrine()->getRepository(Operation::class)->findBy(array('code'=>$operation->getCode()));

        if($op==null){
            return new Response('Ce code est erroné, veuillez réessayer', Response::HTTP_CREATED);

        }else{
            

        if(count($op)>1){
            return new Response('Cet envoi a déja été retiré', Response::HTTP_CREATED);

        }else{
            
            $client =$this->getDoctrine()->getRepository(Client::class)->findBy(array('id'=>$op[0]->getClient()));
            $operation->setDate(new \Datetime());
            $operation->setUtilisateur($id);
            $operation->setMontant($op[0]->getMontant());
            $operation->setClient($op[0]->getClient());
            $operation->setCode($op[0]->getCode());
            $operation->setFrais($op[0]->getFrais());
            $operation->setType($type);
           
            $f=$op[0]->getFrais();
            $compte->setSolde($compte->getSolde()+$operation->getMontant());

            $commission = new Commission();
            $form2= $this->createForm(CommissionType::class, $commission);
            $form2->handleRequest($request);
            $form2->submit($data);
            $commission->setEtat(0);
            $commission->setSysteme(0);
            $commission->setPartenaire(($f*10)/100);
            $commission->setOperation($operation);
            $entityManager = $this->getDoctrine()->getManager();

            $cl = new Client();
            $form2= $this->createForm(ClientType::class, $cl);
            $form2->handleRequest($request);
            $form2->submit($data);
            $client[0]->setNcibeneficiaire($cl->getNcibeneficiaire());

        
            $entityManager->persist($commission);

            $entityManager->persist($operation);
    
            $entityManager->flush();

            return new Response('Retrait effectué avec succés', Response::HTTP_CREATED);
        
            }

        }

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
