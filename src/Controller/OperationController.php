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
use App\Repository\ClientRepository;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("api/operation")
 */
class OperationController extends AbstractController
{

    /**
     * @Route("/envoi", name="operation_envoi", methods={"GET","POST"})
     */
    public function envoi(Request $request,EntityManagerInterface $entityManager, ValidatorInterface $validator,SerializerInterface $serializer): Response
    {
        $user=$this->getUser();
        $id=$this->getUser()->getId();
        $part=$this->getUser()->getPartenaire()->getId();
        $partenaire=$this->getUser()->getPartenaire();
        $compte= $user->getCompte();
        
        $type=$this->getDoctrine()->getRepository(Type::class)->findOneBy(array('libelle'=>'envoi'));

        $operation = new Operation();
        $form1 = $this->createForm(OperationType::class, $operation);
        $form1->handleRequest($request);
        $data=$request->request->all();
        $form1->submit($data);
        $montant=$operation->getMontant();


        if($compte->getSolde()>=10000){
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $client->setNcibeneficiaire(1) ;
        $client->setPartanaire($partenaire) ;
        $client->setMontant($montant) ;
        $form->handleRequest($request);
        $form->submit($data);

       
        if($montant<=$compte->getSolde() && $montant>=500){

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
        $commission->setUtilisateur($partenaire);

        $errors = $validator->validate($client);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $m = $validator->validate($operation);
            if(count($m)) {
                $m = $serializer->serialize($m, 'json');
                return new Response($m, 500, [
                    'Content-Type'=>  'application/json'
                ]);
            }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commission);
        $entityManager->persist($client);
        $entityManager->persist($operation);

        $entityManager->flush();
        }else{
            return new Response('Le montant à envoyer doit être inférieur au solde de votre compte et supérieur à 500', 500, [
                'Content-Type'=>  'application/json'
            ]);
        }
        $info=" les frais d'envoi: ".$f." le code d'envoi: ".$code;
        return new JsonResponse('Envoi reussi'.$info,200, [
            'Content-Type'=>  'application/json'
        ]);
        }else{
            return new JsonResponse('Vous ne pouvez pas faire d\'envoi le solde de votre compte est bas',500, [
                'Content-Type'=>  'application/json'
            ]);
        }
    }

    /**
     * @Route("/retrait", name="operation_retrait", methods={"GET","POST"})
     */
    public function retrait(Request $request,EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $id=$this->getUser();
        $type=$this->getDoctrine()->getRepository(Type::class)->findOneBy(array('libelle'=>'retrait'));
        $partenaire=$this->getUser()->getPartenaire();

        $compte= $this->getUser()->getCompte();
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $data=$request->request->all();
        $form->handleRequest($request);
        $form->submit($data);

        $op=$this->getDoctrine()->getRepository(Operation::class)->findBy(array('code'=>$operation->getCode()));
        if($op==null){
            return new JsonResponse('Ce code est erroné, veuillez réessayer',500, [
                'Content-Type'=>  'application/json'
            ]);
        }else{
            

        if(count($op)>1){
            return new JsonResponse('Cet envoi a déja été retiré',500, [
                'Content-Type'=>  'application/json'
            ]);
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
            $commission->setUtilisateur($partenaire);
            $commission->setOperation($operation);
            $entityManager = $this->getDoctrine()->getManager();

            $cl = new Client();
            $form2= $this->createForm(ClientType::class, $cl);
            $form2->handleRequest($request);
            $form2->submit($data);
            $cl->setPartanaire($partenaire);
            $cl->setNomenvoyeur($client[0]->getNomenvoyeur());
            $cl->setPrenomenvoyeur($client[0]->getPrenomenvoyeur());
            $cl->setTelephoneenvoyeur($client[0]->getTelephoneenvoyeur());
            $cl->setNcienvoyeur($client[0]->getNcienvoyeur());
            $cl->setNombeneficiaire($client[0]->getNombeneficiaire());
            $cl->setPrenombeneficiaire($client[0]->getPrenombeneficiaire());
            $cl->setTelephonebeneficiaire($client[0]->getTelephonebeneficiaire());
            $cl->setNcibeneficiaire($cl->getNcibeneficiaire());
            $cl->setMontant($op[0]->getMontant()) ;


            $entityManager->persist($commission);

            $entityManager->persist($operation);
            $entityManager->persist($cl);

    
            $entityManager->flush();

            return new JsonResponse('Retrait effectué avec succés',200, [
                'Content-Type'=>  'application/json'
            ]);
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
