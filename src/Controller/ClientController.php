<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Repository\CommissionRepository;
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
        $clients=$clientRepository->findAll();
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
        $clients=$com->findAll();
        $data = $serializer->serialize($clients, 'json',['groups' => ['coms']]);
        return new Response($data, 200, [
            'Content-Type'=>  'application/json'
        ]);
    }

}
