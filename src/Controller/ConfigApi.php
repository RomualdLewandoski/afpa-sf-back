<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigApi extends AbstractController
{


    /**
     * @Route("/api", name="api-info")
     */
    public function index(): JsonResponse
    {
        $obj = new \stdClass();
        $obj->api = "listening";
        return new JsonResponse($obj);
    }

    /**
     * @Route("/api/config", name="api_getConfig")
     * @param Request $request
     * @return JsonResponse
     */
    public function getConfig(Request $request): JsonResponse
    {
        $obj = new \stdClass();
        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('App:Config')->find(1);
        if ($config == null) {
            $obj->state = 1;
        } else {
            $users = $em->getRepository('App:Users')->findAll();
            $features = $em->getRepository('App:Features')->findAll();
            $ratings = $em->getRepository('App:Ratings')->findAll();
            $count = count($users);
            $aFeatures = array();
            $aRatings = array();
            foreach ($features as $feature) {
                    $json = new \stdClass();
                    $json->id = $feature->getId();
                    $json->title =$feature->getTitle();
                    $json->sub = $feature->getSubtitle();
                    $json->img = $feature->getImg();
                    array_push($aFeatures, $json);
            }
            foreach ($ratings as $item){
               $userId =  $item->getUserId();
               $user_temp = $em->getRepository('App:Users')->find($userId);
               if ($user_temp!= null){
                   $json = new \stdClass();
                   $json->id = $item->getId();
                   $json->prenom =$user_temp->getPrenom();
                   $json->nom = $user_temp->getNom();
                   $json->comments = $item->getComment();
                   $json->stars = $item->getStars();
                   $json->img = $this->get_gravatar($user_temp->getEmail());
                   array_push($aRatings, $json);
               }
            }

            $obj->state = 0;
            $obj->subtitle = $config->getSubtitle();
            $obj->features = $aFeatures;
            $obj->ratings = $aRatings;
            $obj->talking = "";
            $obj->users = $count;
            $obj->projects = 1;
            $obj->cafe = $config->getCafe();
            $obj->codes = 154230;
        }
        return new JsonResponse($obj, Response::HTTP_OK);

    }


    function get_gravatar($email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array())
    {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val)
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }

    //todo chargement des pages CGU CGV RGPD & TEAM suivant l'url de l'utilisateur
}