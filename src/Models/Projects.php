<?php


namespace Models;


use App\Entity\Members;
use Config\Premium_Config;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class Projects
{
    private $em;


    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function getProjectList($token): \stdClass
    {
        $obj = new \stdClass();
        $auth = new AuthModel();
        $user = $this->em->getRepository('App:Users')->findOneBy(array('email' => $auth->getTokenInfo($token)));
        $projects = $this->em->getRepository('App:Members')->findBy(array('user_id' => $user->getId()));
        $nb_projects = count($projects);
        $obj->state = 0;
        if ($nb_projects == 0) {
            $obj->count = 0;
        } else {
            $aProjects = array();
            foreach ($projects as $project) {
                $item = $this->em->getRepository('App:Project')->find($project->getProjectId());
                if ($item != null) {
                    $json = new \stdClass();
                    $json->id = $item->getId();
                    $json->nom = $item->getNom();
                    $json->created = date("d/m/Y H:i", $item->getCreatedTime());
                    $json->description = $item->getDescription();
                    $json->ownerId = $item->getOwnerId();
                    $begin_date = $item->getBeginDate();
                    $begin_timestamp = strtotime($begin_date);
                    $json->beginDate = date("d/m/Y", $begin_timestamp);
                    $end_date = $item->getEndDate();
                    $end_timestamp = strtotime($end_date);
                    $json->endDate = date("d/m/Y", $end_timestamp);
                    $json->invite = $item->getInvite();
                    array_push($aProjects, $json);
                }

            }
            $obj->count = count($aProjects);
            $obj->projects = $aProjects;
        }
        return $obj;
    }


    public function addProject(Request $request, $token): \stdClass
    {
        $auth = new AuthModel();
        $obj = new \stdClass();
        $config = new Premium_Config();
        $user = $this->em->getRepository('App:Users')->findOneBy(array('email' => $auth->getTokenInfo($token)));
        $nom = $request->get('pName');
        $createdTime = time();
        $description = $request->get('pDesc');
        $ownerId = $user->getId();
        $beginDate = $request->get('pStart');
        $endDate = $request->get('pEnd');
        $utils = new Utils();
        $invite = base64_encode($utils->generateRandomString());
        if (empty($nom) OR
            empty($createdTime) OR
            empty($description) OR
            empty($ownerId) OR
            empty($beginDate) OR
            empty($endDate) OR
            empty($invite)
        ) {
            $obj->state = 1;
            $obj->error = "Des champs sont manquants dans le formulaire de création de projet";
        } else {
            if ($user->getPremium()) {
                $flag = true;
            } else {
                $projects = $this->em->getRepository('App:Project')->findBy(array('ownerId' => $ownerId));
                if (count($projects) >= $config->max_project) {
                    $flag = false;
                } else {
                    $flag = true;
                }
            }
            if ($flag) {
                $project = new \App\Entity\Project();
                $project->setNom($nom);
                $project->setCreatedTime($createdTime);
                $project->setDescription($description);
                $project->setOwnerId($ownerId);
                $project->setBeginDate($beginDate);
                $project->setEndDate($endDate);
                $project->setInvite($invite);
                $this->em->persist($project);
                $this->em->flush();
                $member = new Members();
                $member->setUserId($ownerId);
                $member->setProjectId($project->getId());
                $member->setRank(2);
                $this->em->persist($member);
                $this->em->flush();
                $obj->state = 0;
                $obj->redirect = "/#/app/view/" . $project->getId();
                $obj->token = $auth->generateJWT($user->getEmail(), $user->getPrenom(), $user->getNom(), $user->getRank());
            } else {
                $obj->state = 1;
                $obj->error = "Limite de projet atteinte envie de plus ? passez à la version premium";
            }
        }
        return $obj;
    }


    public function isProjectPremium($id_project): bool
    {
        $project = $this->em->getRepository('App:Project')->find($id_project);
        $owner = $this->em->getRepository('App:Users')->find($project->getOwnerId());
        return $owner->getPremium();

    }

    public function isMember($user_id, $project_id): bool
    {
        $members = $this->em->getRepository('App:Members')->findOneBy(array('user_id' => $user_id, 'project_id' => $project_id));
        return $members != null ;
    }
}