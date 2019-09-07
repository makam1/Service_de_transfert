<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Operation;
use App\Entity\Commission;
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
                $clients =$this->getDoctrine()->getRepository(Client::class)->findBy(array('id'=>$op[0]->getClient()));
                $montant =$op[0]->getMontant();

                $data = $serializer->serialize($clients, 'json',['groups' => ['clients']]);
                return new Response($data, 200, [
                'Content-Type'=>  'application/json'
                ]);
            }
        }
    }
}
