<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractFOSRestController
{
//    /**
//     * @Route("/recipe", name="recipe")
//     */
//    public function index()
//    {
//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/RecipeController.php',
//        ]);
//    }

    private function generateRecipes() {
        $recipes = [];
        $titles = array("Receptas1", "Receptas2", "Receptas3", "Receptas4", "Receptas5");
        for ($i = 0; $i < 5; $i++) {
            $recipe = [];
            $recipe['id'] = $i;
            $recipe['title'] = $titles[$i];
            $recipe['ingredients'] = $this->generateIngredients();
            $recipe['description'] = "blablabla";
            $recipes[] = $recipe;
        }
        return $recipes;
    }

    private function generateIngredients() {
        $result = [];
        $ingredients = array("kiausiniai", "pienas", "druska", "duona", "aliejus", "vistiena", "jautiena", "lasisa",
            "pomidoras", "agurkai", "sviestas", "varske");
        for ($i = 0; $i < 5; $i++) {
            $result[] = $ingredients[array_rand($ingredients)];
        }
        return $result;
    }

    /**
     * @Rest\Get("/api/recipes")
     */
    public function getRecipes() {
        $recipes = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->findAll();
        return $recipes;
    }

    /**
     * @Rest\Get("/api/recipes/{id_recipe}")
     */
    public function getRecipeById($id_recipe) {
        $recipe = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->find($id_recipe);
        if(!$recipe) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        return $recipe;
    }

    /**
     * @Rest\Post("/api/recipes")
     */
    public function createRecipe() {
//        $request = Request::createFromGlobals();
//        $title = $request->get("title");
//        $description = $request->get("description");
//
//        $recipe = [];
//        $recipe['id'] = rand(6,100);
//        $recipe['title'] = $title;
//        $recipe['ingredients'] = [];
//        $recipe['description'] = $description;
//
//        $response = array("status" => "created", "recipe" => $recipe);
//        $res = new JsonResponse($response);
//        $res->setStatusCode(201);
//        return $res;
        // Database
        $entityManager = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent());
        $recipe = new Recipe();
        $recipe->setTitle($data->title);
        $recipe->setDescription($data->description);
        $recipe->setPrepTime((int)$data->prepTime);
        $ingredientIds = $data->ingredients;
        for ($i = 0; $i < sizeof($ingredientIds); $i++) {
            $ingredient = $this->getDoctrine()
                ->getRepository(Ingredient::class)
                ->find($ingredientIds[$i]);
            if(!$ingredient) {
                $res = new JsonResponse();
                $res->setStatusCode(204);
                return $res;
            }
            $recipe->addIngredient($ingredient);
        }

        $entityManager->persist($recipe);
        $entityManager->flush();

        $response = array("status" => "created", "recipe" => $recipe->jsonSerialize());
        $res = new JsonResponse($response);
        $res->setStatusCode(201);
        return $res;
    }

    /**
     * @Rest\Get("/api/recipes/{id_recipe}/ingredients")
     */
    public function getIngredientsOfRecipe($id_recipe) {
//        $recipes = $this->generateRecipes();
//        if($id_recipe >= sizeof($recipes)) {
//            $response = array("status" => "error", "recipes" => $recipes);
//            $res = new JsonResponse($response);
//            $res->setStatusCode(204);
//            return $res;
//        }
//        $recipe = $recipes[$id_recipe];
//        $ingredients = $recipe["ingredients"];
//        return $ingredients;
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)->find($id_recipe);
        if(!$recipe) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        return $recipe->getIngredients();
    }

    /**
     * @Rest\Get("/api/recipes/{id_recipe}/ingredients/{id_ingredient}")
     */
    public function getIngredientOfRecipe($id_recipe, $id_ingredient) {
//        $recipes = $this->generateRecipes();
//        if($id_recipe >= sizeof($recipes)) {
//            $response = array("status" => "error", "recipes" => $recipes);
//            $res = new JsonResponse($response);
//            $res->setStatusCode(204);
//            return $res;
//        }
//        $recipe = $recipes[$id_recipe];
//        $ingredients = $recipe["ingredients"];
//        if($id_ingredient >= sizeof($recipe["ingredients"])) {
//            $response = array("status" => "error", "ingredients" => $ingredients);
//            $res = new JsonResponse($response);
//            $res->setStatusCode(204);
//            return $res;
//        }
//        return $ingredients[$id_ingredient];
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)->find($id_recipe);
        if(!$recipe) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        $ingredients = $recipe->getIngredients();
        if(!$ingredients[$id_ingredient]) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        return $ingredients[$id_ingredient];
    }

    /**
     * @Rest\Put("/api/recipes/{id_recipe}")
     */
    public function updateRecipe($id_recipe) {
//        $request = Request::createFromGlobals();
//        $title = $request->get("title");
//        $description = $request->get("description");
//
//        $recipes = $this->generateRecipes();
//        $recipe = [];
//        $recipe['title'] = $title;
//        $recipe['description'] = $description;
//
//        if($id_recipe >= sizeof($recipes) or $id_recipe < 0) {
//            $res = new JsonResponse();
//            $res->setStatusCode(204);
//            return $res;
//        }
//
//        $response = array("status" => "updated", "recipe" => $recipe);
//        $res = new JsonResponse($response);
//        $res->setStatusCode(200);
//        return $res;
        $entityManager = $this->getDoctrine()->getManager();

        // Getting request body
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent());

        // Finding recipe
        $recipe = $this->getRecipeById($id_recipe);
        if(!$recipe) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }

        // Editing recipe values
        $recipe->setTitle($data->title);
        $recipe->setDescription($data->description);
        $recipe->setPrepTime((int)$data->prepTime);
        $ingredientIds = $data->ingredients;
        for ($i = 0; $i < sizeof($ingredientIds); $i++) {
            $ingredient = $this->getDoctrine()
                ->getRepository(Ingredient::class)
                ->find($ingredientIds[$i]);
            if(!$ingredient) {
                $res = new JsonResponse();
                $res->setStatusCode(204);
                return $res;
            }
            $recipe->addIngredient($ingredient);
        }

        // Pushing new data to database
        $entityManager->persist($recipe);
        $entityManager->flush();

        // Returning response
        $response = array("status" => "updated", "recipe" => $recipe->jsonSerialize());
        $res = new JsonResponse($response);
        $res->setStatusCode(201);
        return $res;
    }

    /**
     * @Rest\Delete("/api/recipes/{id_recipe}")
     */
    public function deleteRecipe($id_recipe) {
        // Finding recipe
        $recipe = $this->getRecipeById($id_recipe);
        if(!$recipe) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }

        // Generating response
        $response = array("status" => "deleted", "recipe" => $recipe->jsonSerialize());
        $res = new JsonResponse($response);
        $res->setStatusCode(200);

        // Updating database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($recipe);
        $entityManager->flush();

        return $res;
    }

    /**
     * @Rest\Delete("/api/recipes/{id_recipe}/ingredients/{id_ingredient}")
     */
    public function deleteIngredientFromRecipe($id_recipe, $id_ingredient) {
        $recipe = $this->getRecipeById($id_recipe);
        if(!$recipe) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        if(!$recipe->removeIngredientById($id_ingredient)){
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }

        // Generating response
        $response = array("status" => "deleted", "recipe" => $recipe->jsonSerialize());
        $res = new JsonResponse($response);
        $res->setStatusCode(200);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($recipe);
        $entityManager->flush();
        return $res;
    }

    /**
     * @Rest\Post("/api/recipes/{id_recipe}/ingredients/{id_ingredient}")
     */
    public function addIngredientToRecipe($id_recipe, $id_ingredient) {
        $recipe = $this->getRecipeById($id_recipe);
        if(!$recipe) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        $entityManager = $this->getDoctrine()->getManager();
        $ingredient = $entityManager->getRepository(Ingredient::class)->find($id_ingredient);
        if(!$ingredient) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        $recipe->addIngredient($ingredient);

        // Generating response
        $response = array("status" => "ingredient added", "recipe" => $recipe->jsonSerialize());
        $res = new JsonResponse($response);
        $res->setStatusCode(200);

        $entityManager->persist($recipe);
        $entityManager->flush();
        return $res;
    }
}
