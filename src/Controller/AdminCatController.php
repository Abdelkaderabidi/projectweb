<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cat')]
class AdminCatController extends AbstractController
{

    #[Route('/', name: 'app_admin_cat_index', methods: ['GET','POST'])]
public function index(CategorieRepository $categorieRepository, Request $request): Response
{
    $categories = null;

    // Vérifie si des paramètres de tri ou de recherche ont été envoyés via POST
    if ($request->isMethod("POST")) {
        if ($request->request->get('optionsRadios')) {
            // Si un tri est demandé
            $sortKey = $request->request->get('optionsRadios');
            switch ($sortKey) {
                case 'NomCat':
                    // Tri par nom de catégorie
                    $categories = $categorieRepository->sortByNomCat();
                    break;
                // Ajoutez d'autres cas pour d'autres types de tri si nécessaire
                default:
                    // Par défaut, chargez toutes les catégories sans tri
                    $categories = $categorieRepository->findAll();
                    break;
            }
        } else {
            // Si une recherche est demandée
            $searchTerm = $request->request->get('Search');
            if ($searchTerm) {
                // Effectue une recherche par nom de catégorie
                $categories = $categorieRepository->searchByNomCat($searchTerm);
            }
        }
    } else {
        // Charge toutes les catégories par défaut si aucun paramètre de tri ou de recherche n'est fourni
        $categories = $categorieRepository->findAll();
    }

    // Rend la vue avec les catégories récupérées
    return $this->render('admin_cat/index.html.twig', [
        'categories' => $categories,
    ]);
}

    #[Route('/front', name: 'app_client_index', methods: ['GET'])]
    public function indexfront(CategorieRepository $categorieRepository): Response
    {
        return $this->render('admin_cat/frontclient.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }


    

    #[Route('/new', name: 'app_admin_cat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_cat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_cat/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{idCat}', name: 'app_admin_cat_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('admin_cat/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{idCat}/edit', name: 'app_admin_cat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_cat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_cat/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{idCat}', name: 'app_admin_cat_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getIdCat(), $request->request->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_cat_index', [], Response::HTTP_SEE_OTHER);
    }
}
