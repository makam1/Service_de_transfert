<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Entity\Operation;
use App\Form\OperationType;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/operation")
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
     * @Route("/new", name="operation_new", methods={"GET","POST"})
     */
    public function new(Request $request,EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
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
        $operation->setCode();
       

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

        $entityManager->persist($commission);
        $entityManager->persist($client);
        $entityManager->persist($operation);

        $entityManager->flush();

        return new Response('Partenaire, compte et admin associé ajouté', Response::HTTP_CREATED);
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

    /**
     * @Route("/{id}", name="operation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Operation $operation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$operation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($operation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('operation_index');
    }
}
