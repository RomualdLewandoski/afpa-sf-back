<?php


namespace App\Controller;


use Models\AuthModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Hotel extends AbstractController
{

    /**
     * @Route("/api/hotel/add", name="addHotel")
     * @param Request $request
     * @return JsonResponse
     */
    public function addHotel(Request $request): JsonResponse
    {
        $obj = new \stdClass();
        if ($request->isMethod('POST')) {
            $token = $request->get("token");
            if ($token != null) {
                $auth = new AuthModel();
                if ($auth->isLogged($token)) {
                    $em = $this->getDoctrine()->getManager();
                    $hotel = new \Models\Hotel($em);
                    $obj = $hotel->addHotel($request, $token);
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
     * @Route("/api/hotel/vote", name="voteHotel")
     * @param Request $request
     * @return JsonResponse
     */
    public function voteHotel(Request $request): JsonResponse
    {
        $obj = new \stdClass();
        if ($request->isMethod('POST')) {
            $token = $request->get('token');
            if ($token != null){
                $auth = new AuthModel();
                if ($auth->isLogged($token)){
                    $em = $this->getDoctrine()->getManager();
                    $hotel = new \Models\Hotel($em);
                    $obj = $hotel->vote($request, $token);
                }else{
                    $obj->state = 1;
                    $obj->error = "Vous devez être connecté";
                    $obj->redirect = "/#/login";
                }
            }else{
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