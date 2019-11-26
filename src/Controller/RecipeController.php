<?php

namespace App\Controller;

use App\Entity\Recipe;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            ->findAll()
        ;
        return $recipes;
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
        $recipe = new Recipe();
        $recipe->setTitle($request->get("title"));
        $recipe->setDescription($request->get("description"));
        $recipe->setPrepTime((int)$request->get("prepTime"));
        $ingredients = $request->get("ingredients");
        for ($i = 0; $i <= sizeof($ingredients); $i++) {
            // array of ingredients contains ingIDs
            $recipe->addIngredient($ingredients[$i]);
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
        $recipes = $this->generateRecipes();
        if($id_recipe >= sizeof($recipes)) {
            $response = array("status" => "error", "recipes" => $recipes);
            $res = new JsonResponse($response);
            $res->setStatusCode(204);
            return $res;
        }
        $recipe = $recipes[$id_recipe];
        $ingredients = $recipe["ingredients"];
        return $ingredients;
    }

    /**
     * @Rest\Get("/api/recipes/{id_recipe}/ingredients/{id_ingredient}")
     */
    public function getIngredientOfRecipe($id_recipe, $id_ingredient) {
        $recipes = $this->generateRecipes();
        if($id_recipe >= sizeof($recipes)) {
            $response = array("status" => "error", "recipes" => $recipes);
            $res = new JsonResponse($response);
            $res->setStatusCode(204);
            return $res;
        }
        $recipe = $recipes[$id_recipe];
        $ingredients = $recipe["ingredients"];
        if($id_ingredient >= sizeof($recipe["ingredients"])) {
            $response = array("status" => "error", "ingredients" => $ingredients);
            $res = new JsonResponse($response);
            $res->setStatusCode(204);
            return $res;
        }
        return $ingredients[$id_ingredient];
    }

    /**
     * @Rest\Put("/api/recipes/{id_recipe}")
     */
    public function updateRecipe($id_recipe) {
        $request = Request::createFromGlobals();
        $title = $request->get("title");
        $description = $request->get("description");

        $recipes = $this->generateRecipes();
        $recipe = [];
        $recipe['title'] = $title;
        $recipe['description'] = $description;

        if($id_recipe >= sizeof($recipes) or $id_recipe < 0) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }

        $response = array("status" => "updated", "recipe" => $recipe);
        $res = new JsonResponse($response);
        $res->setStatusCode(200);
        return $res;
    }

    /**
     * @Rest\Delete("/api/recipes/{id_recipe}/ingredients/{id_ingredient}")
     */
    public function deleteIngredientFromRecipe($id_recipe, $id_ingredient) {
        $recipes = $this->generateRecipes();
        if($id_recipe >= sizeof($recipes)) {
            $response = array("status" => "error", "recipes" => $recipes);
            $res = new JsonResponse($response);
            $res->setStatusCode(204);
            return $res;
        }
        $recipe = $recipes[$id_recipe];
        $ingredients = $recipe["ingredients"];
        if($id_ingredient >= sizeof($recipe["ingredients"])) {
            $response = array("status" => "error", "ingredients" => $ingredients);
            $res = new JsonResponse($response);
            $res->setStatusCode(204);
            return $res;
        }
        $response = array("status" => "deleted", "ingredient" => $ingredients[$id_ingredient]);
        $res = new JsonResponse($response);
        $res->setStatusCode(200);
        return $res;
    }
}
