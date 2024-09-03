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
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/user/prod')]
class UserProdController extends AbstractController
{
    private $entityManager;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    #[Route('/add-to-favorites/{id}', name: 'add_to_favorites', methods: ['POST'])]
    public function addToFavorites(int $id, Request $request): Response
    {
        // Fetch the product by ID
        $produit = $this->entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return new Response('Product not found', Response::HTTP_NOT_FOUND);
        }

        // Increment the `fav` field
        $currentFav = $produit->getFav() ?? 0;
        $produit->setFav($currentFav + 1);

        // Save the changes to the database
        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
   



    

    
    

    #[Route('/front', name: 'app_client_prod_index', methods: ['GET'])]
    public function indexfront(ProduitRepository $produitRepository): Response
    {
        
        return $this->render('admin_prod/frontclient.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }
    
    

   
    

   



  }

