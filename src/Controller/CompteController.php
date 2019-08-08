<?php
namespace App\Controller;
use App\Entity\Compte;
use App\Form\CompteType;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * @Route("/api/compte")
 */
class CompteController extends AbstractController
{
    
    /**
     * @Route("/new", name="comptenew", methods={"GET","POST"})
     */
    public function new(Request $request,SerializerInterface $serializer,EntityManagerInterface $entityManager, ValidatorInterface $validator ): Response
    {

        $compte = new Compte();
        $form = $this->createForm(CompteType::class,$compte);
        $data=$request->request->all();
        $form->submit($data);
        if($form->isSubmitted()){
            $date=date("Y").date("m").date("d").date("H").date("i").date("s");
            $compte->setNumerocompte($date);
            $compte->setSolde(0);
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
        
        return new Response('Le compte a été ajouté',Response::HTTP_CREATED);
    }
       
        return new Response('Vous devez renseigner les informations du compte ',Response::HTTP_CREATED );
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
    /**
     * @Route("/{id}", name="compte_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Compte $compte): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compte->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compte);
            $entityManager->flush();
        }
        return $this->redirectToRoute('compte_index');
    }
}