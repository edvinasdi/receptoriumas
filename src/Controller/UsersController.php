<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UsersController extends AbstractFOSRestController
{
//    /**
//     * @Route("/users", name="users")
//     */
//    public function index()
//    {
//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/UsersController.php',
//        ]);
//    }

    private function generateUsers() {
        $users = [];
        $emails = array("raides@sbcglobal.net", "dbindel@yahoo.com", "jorgb@comcast.net", "arnold@aol.com",
            "rattenbt@att.net", "vganesh@outlook.com", "bowmanbs@att.net", "dieman@sbcglobal.net", "scato@me.com",
            "oechslin@hotmail.com");
        $names = array("Peter", "Ingrid", "Johnny", "Oliver", "Kevin", "Anthony", "Simon", "Arthur", "Christopher",
            "Arnold");
        for ($i = 0; $i < 10; $i++) {
            $user = [];
            $user['id'] = $i;
            $user['e-mail'] = $emails[$i];
            $user['password'] = $this->generateRandomString();
            $user['name'] = $names[$i];
            $user['birthday'] = $this->generateBirthday();
            $users[] = $user;
        }
        return $users;
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function generateBirthday() {
        $curr_year = date('Y');
        $birth_year  = rand($curr_year-18,$curr_year-47);
        $birth_month = rand(1,12);
        $birth_day   = rand(1,30);
        $birthday = $birth_year.'-'.$birth_month.'-'.$birth_day;
        return $birthday;
    }

    /**
     * @Rest\Post("/api/users")
     */
    public function createUser()
    {
        // Hardcoded data
//        $request = Request::createFromGlobals();
//        $email = $request->get("email");
//        $password = $request->get("password");
//        $name = $request->get("name");
//        $birthday = $request->get("birthday");
//
//        $user = [];
//        $user['id'] = rand(10, 100);
//        $user['e-mail'] = $email;
//        $user['password'] = $password;
//        $user['name'] = $name;
//        $user['birthday'] = $birthday;
//
//        $response = array("status" => "created", "user" => $user);
//        $res = new JsonResponse($response);
//        $res->setStatusCode(201);
//        return $res;

        // Database
        $entityManager = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();

        $user = new User();
        $user->setEMail($request->get("email"));
        $user->setPassword($request->get("password"));
        $user->setName($request->get("name"));
        $dateString = $request->get("birthday");
        $user->setBirthday(\DateTime::createFromFormat('Y-m-d', $dateString));

        $entityManager->persist($user);
        $entityManager->flush();

        $response = array("status" => "created", "user" => $user->jsonSerialize());
        $res = new JsonResponse($response);
        $res->setStatusCode(201);
        return $res;
    }

    /**
     * @Rest\Get("/api/users")
     */
    public function getUsers()
    {
        return $this->generateUsers();
    }

    /**
     * @Rest\Get("/api/users/{id_user}")
     */
    public function getUserById($id_user)
    {
        $users = $this->generateUsers();
        if($id_user >= sizeof($users) or $id_user < 0) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        return $users[$id_user];
    }

    /**
     * @Rest\Put("/api/users/{id_user}")
     */
    public function updateUser($id_user)
    {
        $users = $this->generateUsers();
        $request = Request::createFromGlobals();
        $email = $request->get("email");
        $password = $request->get("password");
        $name = $request->get("name");
        $birthday = $request->get("birthday");

        if($id_user >= sizeof($users) or $id_user < 0) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }

        $user = $users[$id_user];
        $user['e-mail'] = $email;
        $user['password'] = $password;
        $user['name'] = $name;
        $user['birthday'] = $birthday;

        $response = array("status" => "updated", "user" => $user);
        $res = new JsonResponse($response);
        $res->setStatusCode(200);
        return $res;
    }

    /**
     * @Rest\Delete("/api/users/{id_user}")
     */
    public function deleteUser($id_user)
    {
        $users = $this->generateUsers();
        if($id_user >= sizeof($users) or $id_user < 0) {
            $res = new JsonResponse();
            $res->setStatusCode(204);
            return $res;
        }
        $deleted = $users[$id_user];
        $response = array("status" => "deleted", "user" => $deleted);
        $res = new JsonResponse($response);
        $res->setStatusCode(200); // 204 jei nepavyko
        return $res;
    }
}
