<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Client;
use App\Entity\Compte;
use App\Form\DepotType;
use App\Entity\Operation;
use App\Entity\Commission;
use App\Form\RecDepotType;
use App\Entity\Utilisateur;
use App\Form\OperationType;
use App\Repository\ClientRepository;
use App\Repository\CommissionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("api/")
 */
class ClientController extends AbstractController
{
    
    /**
     * @Route("client", name="client_liste", methods={"GET"})
     */
    public function liste(ClientRepository $clientRepository,SerializerInterface $serializer): Response
    {
        $id=$this->getUser()->getPartenaire()->getId();
        $clients=$this->getDoctrine()->getRepository(Client::class)->findBy(array('partanaire'=>$id));
        $data = $serializer->serialize($clients, 'json',['groups' => ['clients']]);
        return new Response($data, 200, [
            'Content-Type'=>  'application/json'
        ]);
    }

    /**
     * @Route("commission", name="commission_liste", methods={"GET"})
     */
    public function commission(CommissionRepository $com,SerializerInterface $serializer): Response
    {

        $id=$this->getUser()->getPartenaire()->getId();
        $comm=$this->getDoctrine()->getRepository(Commission::class)->findBy(array('utilisateur'=>$id));
        $data = $serializer->serialize($comm, 'json',['groups' => ['coms']]);
        return new Response($data, 200, [
            'Content-Type'=>  'application/json'
        ]);
    }
    

    /**
     * @Route("info", name="info", methods={"GET","POST"})
     */
    public function information(Request $request,SerializerInterface $serializer): Response
    {
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
                $montant =$op[0]->getMontant();
                $clients =$this->getDoctrine()->getRepository(Client::class)->findBy(array('id'=>$op[0]->getClient()));
                
                $data = $serializer->serialize($clients, 'json',['groups' => ['clients']]);
                return new Response($data, 200, [
                'Content-Type'=>  'application/json'
                ]);
            }
        }
    }

    /**
     * @Route("depot/recherche", name="depot", methods={"GET","POST"})
     */
    public function recherche(Request $request,SerializerInterface $serializer): Response
    {
        $depot = new Depot();
        $form = $this->createForm(RecDepotType::class, $depot);
        $data=$request->request->all();
        $form->handleRequest($request);
        $depot->setMontant(1);
        $form->submit($data);
        

        $num=$this->getDoctrine()->getRepository(Compte::class)->findOneBy(array('numerocompte'=>$depot->getNumerocompte()));
        if($num==null){
            return new Response('Ce compte n\'existe pas', Response::HTTP_CREATED);

        }else{  

            $user =$this->getDoctrine()->getRepository(Utilisateur::class)->findBy(array('compte'=>$num->getId()));
            
            $data = $serializer->serialize($user, 'json',['groups' => ['users']]);
            return new Response($data, 200, [
            'Content-Type'=>  'application/json'
            ]);
        }
    }
}
