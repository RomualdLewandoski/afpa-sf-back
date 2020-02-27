<?php


namespace App\Controller;


use Models\AuthModel;
use Models\Projects;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Project extends AbstractController
{

    /**
     * @Route("/api/project/get", name="getProject")
     */
    public function getProject(Request $request): JsonResponse
    {
        $obj = new \stdClass();
        if ($request->isMethod("POST")) {
            $token = $request->get("token");
            $auth = new AuthModel();
            if (!empty($token) && $auth->isLogged($token)) {
                $projectId = $request->get('projectId');
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('App:Users')->findOneBy(array('email' => $auth->getTokenInfo($token)));
                $project = $em->getRepository('App:Project')->find($projectId);
                if ($project != null) {
                    $projectModel = new Projects($em);
                    if ($projectModel->isMember($user->getId(), $projectId)) {
                        $members = $em->getRepository('App:Members')->findBy(array('project_id' => $projectId));
                        $obj->state = 0;
                        $obj->nom = $project->getNom();
                        $obj->premium = $projectModel->isProjectPremium($projectId);
                        $obj->members = count($members);
                        $begin_date = $project->getBeginDate();
                        $begin_timestamp = strtotime($begin_date);
                        $obj->beginDate = date("d/m/Y", $begin_timestamp);
                        $end_date = $project->getEndDate();
                        $end_timestamp = strtotime($end_date);
                        $obj->endDate = date("d/m/Y", $end_timestamp);
                        $activity = new \Models\Activity($em);
                        $aActivity = $activity->getActivity($projectId);
                        $obj->activityCount = count($aActivity);
                        $obj->activity = $aActivity;
                        $hotel = new \Models\Hotel($em);
                        $aHotel = $hotel->getHotel($projectId);
                        $obj->hotelCount = count($aHotel);
                        $obj->hotel = $aHotel;
                        //todo add Budget HERE

                    } else {
                        $obj->state = 2;
                        $obj->error = "Vous n'êtes pas membre de ce projet";
                        $obj->redirect = "/#/app";
                    }
                } else {
                    $obj->state = 2;
                    $obj->error = "Le projet n'existe pas";
                    $obj->redirect = "/#/app/view";
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
     * @Route("/api/project/list", name="listProject")
     * @param Request $request
     * @return JsonResponse
     */
    public function getProjectList(Request $request): JsonResponse
    {
        $obj = new \stdClass();
        if ($request->isMethod('POST')) {
            $token = $request->get("token");
            $auth = new AuthModel();
            if (!empty($token) && $auth->isLogged($token)) {
                $em = $this->getDoctrine()->getManager();
                $projects = new Projects($em);
                $obj = $projects->getProjectList($token);
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
     * @Route("/api/project/add", name="addProject")
     */
    public function addProject(Request $request): JsonResponse
    {
        $obj = new \stdClass();
        if ($request->isMethod('POST')) {
            $token = $request->get("token");
            if ($token != null) {
                $auth = new AuthModel();
                if ($auth->isLogged($token)) {
                    $projects = new Projects($this->getDoctrine()->getManager());
                    $obj = $projects->addProject($request, $token);
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