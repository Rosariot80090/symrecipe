<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recipe', name: 'recipe.index', methods:['GET'])]
    public function index(
        RecipeRepository $repository, 
        PaginatorInterface $paginator, 
        Request $request
        ): Response
    {
        $recipes = $paginator->paginate(
           
            $repository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
       
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
/**
 * This controller allow us to create a new recipe
 *
 * @param Request $request
 * @param EntityManagerInterface $manager
 * @return Response
 */
    #[Route('/recette/creation', name: 'recipe.new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager):Response
    {
        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe=$form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créée avec succès!'
            );


            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

           /**
          * This controller allow us to edit a recipe
          *
          * @param Recipe $recipe
          * @param Request $request
          * @param EntityManagerInterface $manager
          * @return Response
          */
         // Je dois récupérer l'ingrédient à modifier par son id dans $repository
         #[Route('/recette/edition/{id}', 'recipe.edit', methods:['GET', 'POST'])]
         // 1ere METHODE
         //public function edit(RecipeRepository $repository, int $id):Response
         
         // 2eme METHODE
      
        public function edit(
            Recipe $recipe, 
            Request $request, 
            EntityManagerInterface $manager
            ):Response
        {
            // 1ere METHODE
            //$recipe =  $repository->findOneBy(["id"=>$id]);
           
            // 2eme METHODE
            $form = $this->createForm(RecipeType::class, $recipe);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) 
            {
                $recipe = $form->getData();
    
                $manager->persist($recipe);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Votre recette a été modifiée avec succès!'
                );
    
                return $this->redirectToRoute('recipe.index');
            }
            
            return $this->render('pages/recipe/edit.html.twig', [
                'form'=>$form->createView()
            ]);
        }
      /**
       * 
       */
        #[Route('/recipe/suppression/{id}', 'recipe.delete', methods: ['GET'])]
        public function delete(
            EntityManagerInterface $manager, 
            Recipe $recipe
            ): Response
        {

    $manager->remove($recipe);
    $manager->flush();

    $this->addFlash(
        'success',
        'Votre recette a été supprimée avec succès!'
    );
    return $this->redirectToRoute('recipe.index');

}
}
    
        

