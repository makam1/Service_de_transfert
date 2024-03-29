<?php
namespace App\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Compte;
use App\Form\DateType;
use App\Form\CompteType;
use App\Entity\Partenaire;
use App\Entity\Utilisateur;
use App\Form\PartenaireType;
use App\Form\UtilisateurType;
use App\Form\ListeOperationType;
use App\Repository\CompteRepository;
use App\Repository\OperationRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



/**
 * @Route("api/partenaire")
 */
class PartenaireController extends AbstractController
{

    /**
     * @Route("/operation", name="partenaire_operation", methods={"GET"})
     */
    public function operation(OperationRepository $operationRepository,SerializerInterface $serializer): Response
    {
       
        $part=$this->getUser()->getPartenaire()->getId();    
        $users=$this->getDoctrine()->getRepository(Utilisateur::class)->findBy(array('partenaire'=>$part));
        
        $oprepo=$operationRepository->findBy(array('utilisateur'=>$users));


        $data = $serializer->serialize($oprepo, 'json',['groups' => ['listes']]);

        return new Response($data, 200, [
            'Content-Type'=>  'application/json'
        ]);  
    }
    
    /**
     * @Route("/liste", name="partenaire_index", methods={"GET"})
     *  
     */
    public function liste(PartenaireRepository $user,SerializerInterface $serializer): Response
    {
        
        $part=$this->getDoctrine()->getRepository(Partenaire::class)->findAll();
        $data = $serializer->serialize($part, 'json',['groups' => ['partenaires']]);
        return new Response($data, 200, [
            'Content-Type'=>  'application/json'
        ]); 
    }

    /**
     * @Route("/new", name="partenaire_new", methods={"GET","POST"})
     */
    public function new( UserPasswordEncoderInterface $encoder,Request $request,UserPasswordEncoderInterface $passwordEncoder,SerializerInterface $serializer,EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);
        $data=$request->request->all();
        $partenaire->setStatut("actif");
        $form->submit($data);

        $compte = new Compte();
        $form1 = $this->createForm(CompteType::class, $compte);
        $form1->handleRequest($request);
        $form1->submit($data);
        $date=date("Y").date("m").date("d").date("H").date("i").date("s");
        $compte->setNumerocompte($date);
        $compte->setSolde(5000);
        $compte->setPartenaire($partenaire);
       

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

        $entityManager->persist($compte);
        $entityManager->persist($partenaire);
        $entityManager->persist($username);

        $entityManager->flush();

        return new JsonResponse('Partenaire, compte et admin associé ajouté', 200, [
            'Content-Type'=>  'application/json'
        ]);

    }


    /** @Route("/{id}", name="partenaire_show", methods={"GET"})
    */
    public function show(Partenaire $partenaire): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("contrat.pdf", [
            "Attachment" => false
        ]);
    }

    /**
     * @Route("/{id}/bloquer", name="bloquer", methods={"GET","POST"})
     */
    public function bloquer(Request $request, Partenaire $partenaire): Response
    {

        if($partenaire->getRaisonsociale()=='système'){
            return new Response('Vous ne pouvez pas bloquer le système', Response::HTTP_CREATED);
        }
        if($partenaire->getStatut()=='actif'){
        $partenaire->setStatut('bloqué');
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse('Partenaire bloqué', 200, [
            'Content-Type'=>  'application/json'
        ]);

        } else{
            $partenaire->setStatut('actif');
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse('Partenaire débloqué', 200, [
            'Content-Type'=>  'application/json'
        ]);

        } 
    
    }
   
}