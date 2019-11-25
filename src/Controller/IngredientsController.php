<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IngredientsController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/api/ingredients")
     */
    public function createIngredient() {
        $request = Request::createFromGlobals();
        $name = $request->get("name");

        $ingredient = [];
        $ingredient['id'] = rand(10, 100);
        $ingredient['name'] = $name;

        $response = array("status" => "created", "ingredient" => $ingredient);
        $res = new JsonResponse($response);
        $res->setStatusCode(201);
        return $res;
    }

    /**
     * @Rest\Get("/api/ingredients")
     */
    public function getIngredients() {
        $ingredients = array("kiausiniai", "pienas", "druska", "duona", "aliejus", "vistiena", "jautiena", "lasisa",
            "pomidoras", "agurkai", "sviestas", "varske");
        return $ingredients;
    }

    /**
     * @Rest\Get("/api/ingredients/{id_ingredient}")
     */
    public function getIngredientById($id_ingredient) {
        $ingredients = array("kiausiniai", "pienas", "druska", "duona", "aliejus", "vistiena", "jautiena", "lasisa",
            "pomidoras", "agurkai", "sviestas", "varske");
        if($id_ingredient >= sizeof($ingredients) or $id_ingredient < 0) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        return $ingredients[$id_ingredient];
    }

    /**
     * @Rest\Put("/api/ingredients/{id_ingredient}")
     */
    public function updateIngredient($id_ingredient) {
        $request = Request::createFromGlobals();
        $name = $request->get("name");

        $ingredients = array("kiausiniai", "pienas", "druska", "duona", "aliejus", "vistiena", "jautiena", "lasisa",
            "pomidoras", "agurkai", "sviestas", "varske");
        if($id_ingredient >= sizeof($ingredients) or $id_ingredient < 0) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        $ingredients[$id_ingredient] = $name;

        $response = array("status" => "updated", "ingredient" => array("id" => $id_ingredient, "name" => $name));
        $res = new JsonResponse($response);
        $res->setStatusCode(200);
        return $res;
    }

    /**
     * @Rest\Delete("/api/ingredients/{id_ingredient}")
     */
    public function deleteIngredient($id_ingredient) {
        $ingredients = array("kiausiniai", "pienas", "druska", "duona", "aliejus", "vistiena", "jautiena", "lasisa",
            "pomidoras", "agurkai", "sviestas", "varske");
        if($id_ingredient >= sizeof($ingredients) or $id_ingredient < 0) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        $response = array("status" => "deleted", "ingredient" => array("id" => $id_ingredient, "name" => $ingredients[$id_ingredient]));
        $res = new JsonResponse($response);
        $res->setStatusCode(200);
        return $res;
    }
}
