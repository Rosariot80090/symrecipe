<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient', methods: ['GET'])]

    public function index(
        IngredientRepository $repository, 
        PaginatorInterface $paginator, 
        Request $request
        ): Response
    //On a fait une injection de dépendence avec IngredientRepository

    {
      
        $ingredients = $paginator->paginate(
            //$query, /* query NOT result */
            $repository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients 
       ]);
    }
    
    /**
     * This controller show a form which an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été crée avec succès!'
            );

            return $this->redirectToRoute('ingredient');
        }
        
            return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Je dois récupérer l'ingrédient à modifier par son id dans $repository
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods:['GET', 'POST'])]
    // 1ere METHODE
    //public function edit(IngredientRepository $repository, int $id):Response
    
    // 2eme METHODE
    public function edit(
        Ingredient $ingredient, 
        Request $request, 
        EntityManagerInterface $manager
        ):Response
    {
        // 1ere METHODE
        //$ingredient =  $repository->findOneBy(["id"=>$id]);
       
        // 2eme METHODE
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès!'
            );

            return $this->redirectToRoute('ingredient');
        }
        
        return $this->render('pages/ingredient/edit.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $manager, 
        Ingredient $ingredient
        ): Response
    {
        // if (!$ingredient) 
        //{
        //  $this->addFlash(
        //    'warning',
        //  'Votre ingrédient n\'a pas été trouvé !'
        //);

    //return $this->redirectToRoute('ingredient');
    
    //}
    
    $manager->remove($ingredient);
    $manager->flush();

    $this->addFlash(
        'success',
        'Votre ingrédient a été supprimé avec succès!'
    );

    return $this->redirectToRoute('ingredient');

    }
}

