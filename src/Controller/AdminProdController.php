<?php

namespace App\Controller;
use App\Service\Mailer; // Importez le service de mail
use App\Service\SmsGenerator;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;
use App\Entity\PdfGeneratorService;


#[Route('/admin/prod')]
class AdminProdController extends AbstractController

{

    private $entityManager;
    private $mailer;
     

   
    public function __construct(EntityManagerInterface $entityManager, Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }
   



    #[Route('/', name: 'app_admin_prod_index', methods: ['GET', 'POST'])]
    public function index(ProduitRepository $produitRepository, Request $request,SmsGenerator $smsGenerator): Response
    {
        $produits = null;
        
        // Vérifie si des paramètres de tri ou de recherche ont été envoyés via POST
        if ($request->isMethod("POST")) {
            $sortKey = $request->request->get('optionsRadios');
            if ($sortKey === 'NomProd') {
                // Tri par nom de produit
                $produits = $produitRepository->sortByNomProd();
            } elseif ($sortKey === 'Prix') {
                // Tri par prix de produit
                $produits = $produitRepository->sortByPrice();
            } elseif ($sortKey === 'Quantite') {
                // Tri par quantité de produit
                $produits = $produitRepository->sortByQuantity();
            } elseif ($sortKey === 'PromotionStartDate') {
                // Tri par date de début de promotion
                $produits = $produitRepository->sortByPromotionStartDate();
            } else {
                // Si une recherche est demandée
                $searchTerm = $request->request->get('Search');
                if ($searchTerm) {
                    // Recherche par nom, prix, quantité ou description de produit
                    $produits = $produitRepository->searchByName($searchTerm);
                    // Si aucun produit n'est trouvé par le nom, recherche par prix
                    if (empty($produits)) {
                        $produits = $produitRepository->findByPrix($searchTerm);
                    }
                    // Si aucun produit n'est trouvé par le prix, recherche par quantité
                    if (empty($produits)) {
                        $produits = $produitRepository->findByQuantite($searchTerm);
                    }
                    // Si aucun produit n'est trouvé par la quantité, recherche par description
                    if (empty($produits)) {
                        $produits = $produitRepository->findByDescription($searchTerm);
                    }
                } else {
                    // Par défaut, chargez tous les produits sans tri
                    $produits = $produitRepository->findAll();
                }
            }
        } else {
            // Charge tous les produits par défaut si aucun paramètre de tri ou de recherche n'est fourni
            $produits = $produitRepository->findAll();
        }

       
        
        
            
        // Rend la vue avec les produits récupérés
        return $this->render('admin_prod/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/sendSms', name: 'send_sms', methods:'POST')]
    public function sendSms(Request $request, SmsGenerator $smsGenerator): Response
    {
       
        $number=$request->request->get('+21629319685');

        $name=$request->request->get('Nutrinet');

        $text=$request->request->get('text');

        $number_test=$_ENV['twilio_to_number'];// Numéro vérifier par twilio. Un seul numéro autorisé pour la version de test.

        //Appel du service
        $smsGenerator->sendSms($number_test ,$name,$text);

        return $this->render('sms/index.html.twig', ['smsSent'=>true]);
    }

    
    #[Route('/pdf', name: 'generator_service3')]
    public function pdfService(ProduitRepository $produitRepository): Response
    { 
        $produits = $produitRepository->findAll(); // Using ProduitRepository to fetch all products

        $html = $this->renderView('pdf/pdf.html.twig', ['produits' => $produits]);
        $pdfGeneratorService = new PdfGeneratorService();
        $pdf = $pdfGeneratorService->generatePdf($html);

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }

    

    #[Route('/front', name: 'app_client_prod_index', methods: ['GET'])]
    public function indexfront(ProduitRepository $produitRepository): Response
    {
        return $this->render('admin_prod/frontclient.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }
    


   
    #[Route('/new', name: 'app_admin_prod_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $produit->getImage();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images'), $filename);
            $produit->setImage($filename);
            $this->entityManager->persist($produit);
            $this->entityManager->flush();

            // Vérifiez si le produit est en promotion
            if ($produit->isEnPromo()) {
                // Appel du service de mail pour envoyer un e-mail
                $this->mailer->sendEmail($produit);
            }

            return $this->redirectToRoute('app_admin_prod_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_prod/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }
    
    


    #[Route('/{refProd}', name: 'app_admin_prod_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('admin_prod/show.html.twig', [
            'produit' => $produit,
        ]);
    }
    
    #[Route('/client/home', name: 'app_admin_prod_home')]
    public function home(): Response
    {
        return $this->render('admin_prod/homepage.html.twig');
    }

    #[Route('/{refProd}/edit', name: 'app_admin_prod_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, Mailer $mailer): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $produit->getImage();
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images'), $filename);
            $produit->setImage($filename);
    
            // Vérifie si la case de promotion est cochée
            if ($produit->isEnPromo()) {
                // Appel du service de messagerie pour envoyer un e-mail
                $this->mailer->sendEmail($produit);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_admin_prod_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('admin_prod/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{refProd}', name: 'app_admin_prod_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getRefProd(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_prod_index', [], Response::HTTP_SEE_OTHER);
    }

   

   #[Route('/by_category/{idCat}', name: 'app_admin_prod_by_category', methods: ['GET'])]
   public function showProductsByCategory( int $idCat, ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
  {
    // Récupérer la catégorie à partir de son ID
    $categorie = $categorieRepository->find($idCat);

    if (!$categorie) {
        throw $this->createNotFoundException('La catégorie n\'existe pas.');
    }

    // Récupérer les produits associés à cette catégorie
    $produits = $produitRepository->findBy(['Categories' => $categorie]);

    return $this->render('admin_prod/products_by_category.html.twig', [
        'categorie' => $categorie,
        'produits' => $produits,
    ]);
}
#[Route('/products/promotion', name: 'products_in_promotion')]
public function productsInPromotion(ProduitRepository $produitRepository): Response
{
    $productsInPromotion = $produitRepository->findBy(['enPromo' => true]);

    // Vous pouvez ensuite passer les produits en promotion à votre modèle Twig et les afficher dans un template
    return $this->render('admin_prod/products_in_promotion.html.twig', [
        'products' => $productsInPromotion,
    ]);
    
}


   



  }

