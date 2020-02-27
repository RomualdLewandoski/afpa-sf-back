<?php


namespace App\Controller;


use Models\AuthModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Activity extends AbstractController
{

    /**
     * @Route("/api/activity/add", name="addActivity")
     * @param Request $request
     * @return JsonResponse
     */
    public function addActivity(Request $request): JsonResponse
    {
        $obj = new \stdClass();
        if ($request->isMethod('POST')) {
            $token = $request->get("token");
            if ($token != null) {
                $auth = new AuthModel();
                if ($auth->isLogged($token)) {
                    $em = $this->getDoctrine()->getManager();

                    $activity = new \Models\Activity($em);
                    $obj = $activity->addActivity($request, $token);
                } else {
                    $obj->state = 1;
                    $obj->error = "Vous devez être connecté";
                    $obj->redirect = "/#/login";
                }
            } else {
                $obj->state = 1;
                $obj->error = "Vous devez être connecté";
                $obj->redirect = "/#/login";
            }
        } else {
            $obj->api = "ready";
        }
        return new JsonResponse($obj);
    }

    /**
     * @Route("/api/activity/vote", name="voteActivity")
     * @param Request $request
     * @return JsonResponse
     */
    public function voteActivity(Request $request): JsonResponse
    {
        $obj = new \stdClass();
        if ($request->isMethod('POST')) {
            $token = $request->get('token');
            if ($token != null) {
                $auth = new AuthModel();
                if ($auth->isLogged($token)) {
                    $em = $this->getDoctrine()->getManager();

                    $activity = new \Models\Activity($em);
                    $obj = $activity->vote($request, $token);

                } else {
                    $obj->state = 1;
                    $obj->error = "Vous devez être connecté";
                    $obj->redirect = "/#/login";
                }
            } else {
                $obj->state = 1;
                $obj->error = "Vous devez être connecté";
                $obj->redirect = "/#/login";
            }
        } else {
            $obj->api = "ready";
        }

        return new JsonResponse($obj);
    }
}