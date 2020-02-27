<?php


namespace Models;


use App\Entity\ActivityVote;
use Config\Premium_Config;
use Doctrine\Persistence\ObjectManager;
use Monolog\Handler\IFTTTHandler;
use Symfony\Component\HttpFoundation\Request;

class Activity
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function addActivity(Request $request, $token): \stdClass
    {
        $obj = new \stdClass();
        $nom = $request->get('aName');
        $duration = $request->get('aDuration');
        $durationValue = $request->get('aDurationParam');
        $price = $request->get('aPrice');
        $description = $request->get('aDescription');
        $number = $request->get('aNumber');
        $street = $request->get('aStreet');
        $zip = $request->get('aZip');
        $city = $request->get('aCity');
        $project_id = $request->get('aProject');

        if (empty($nom) OR
            empty($duration) OR
            empty($durationValue) OR
            empty($description) OR
            empty($number) OR
            empty($street) OR
            empty($zip) OR
            empty($city) OR
            empty($project_id)) {
            $obj->state = 3;
            $obj->error = "Des champs sont manquants dans le formulaire de création d'activité";
        } else {
            if ($price == null) {
                $price = 0;
            }
            $auth = new AuthModel();
            $user = $this->em->getRepository('App:Users')->findOneBy(array('email' => $auth->getTokenInfo($token)));
            $projectModel = new Projects($this->em);
            if ($projectModel->isMember($user->getid(), $project_id)) {
                $premium = new Premium_Config();
                $flag = false;
                if ($projectModel->isProjectPremium($project_id)) {
                    $flag = true;
                } else if ($this->countActivity($project_id) < $premium->max_activity) {
                    $flag = true;
                }
                if ($flag) {
                    $mapApi = new MapApi();
                    $address_api = $number . "+" . $street . "+" . $city . "+" . $zip;
                    $adresse = $number . " " . $street . " " . $city . " " . $zip;
                    $location = $mapApi->getLocation($address_api);
                    $activity = new \App\Entity\Activity();
                    $activity->setNom($nom);
                    $activity->setDuration($duration);
                    $activity->setDurationParam($durationValue);
                    $activity->setPrice($price);
                    $activity->setDescription($description);
                    $activity->setLat($location->latitude);
                    $activity->setLongitude($location->longitude);
                    $activity->setAddress($adresse);
                    $activity->setUserId($user->getId());
                    $activity->setVotesUp(0);
                    $activity->setVotesDown(0);
                    $activity->setState(0);
                    $activity->setProjectId($project_id);
                    $this->em->persist($activity);
                    $this->em->flush();
                    $obj->state = 0;
                    $json = new \stdClass();
                    $json->id = $activity->getId();
                    $json->nom = $nom;
                    $json->duration = $duration;
                    $json->durationParam = $durationValue;
                    $json->price = $price;
                    $json->description = $description;
                    $json->latitude = $location->latitude;
                    $json->longitude = $location->longitude;
                    $json->addresse = $adresse;
                    $json->user_id = $user->getId();
                    $json->votes_up = 0;
                    $json->votes_down = 0;
                    $json->state = 0;
                    $json->project_id = $project_id;
                    $obj->activity = json_encode($json);
                } else {
                    $obj->state = 3;
                    $obj->error = "Limite d'activité atteinte envie de plus ? passez à la version premium";
                }

            } else {
                $obj->state = 2;
                $obj->error = "Vous n'êtes pas membre de ce projet vous ne pouvez donc pas soumettre d'activités ou d'hotels";
            }
        }

        return $obj;
    }

    public function getActivity($projectId): array
    {
        $aActivity = array();
        $project = $this->em->getRepository('App:Project')->find($projectId);
        if ($project != null) {
            $activities = $this->em->getRepository('App:Activity')->findBy(array('project_id' => $projectId));
            foreach ($activities as $item):
                $json = new \stdClass();
                $json->id = $item->getId();
                $json->nom = $item->getNom();
                $json->duration = $item->getDuration();
                $json->durationParam = $item->getDurationParam();
                $json->price = $item->getPrice();
                $json->description = $item->getDescription();
                $json->latitude = $item->getLat();
                $json->longitude = $item->getLongitude();
                $json->addresse = $item->getAddress();
                $json->user_id = $item->getUserId();
                $json->votes_up = $item->getVotesUp();
                $json->votes_down = $item->getVotesDown();
                $json->state = $item->getState();
                $json->project_id = $projectId;
                array_push($aActivity, $json);
            endforeach;
        }
        return $aActivity;
    }

    public function vote(Request $request, string $token): \stdClass
    {
        $obj = new \stdClass();
        $type = $request->get('type');
        $item_id = $request->get('item_id');
        if (empty($type) OR empty($item_id)) {
            $obj->state = 3;
            $obj->error = "Des paramètres sont manquants dans la requête de vote";
        } else {
            $activity = $this->em->getRepository('App:Activity')->find($item_id);
            if ($activity == null) {
                $obj->state = 2;
                $obj->error = "L'activité n'existe pas";
            } else {
                $project = $this->em->getRepository('App:Project')->find($activity->getProjectId());
                if ($project == null) {
                    $obj->state = 2;
                    $obj->error = "Le projet n'existe pas";
                } else {
                    $auth = new AuthModel();
                    $user = $this->em->getRepository('App:Users')->findOneBy(array('email' => $auth->getTokenInfo($token)));
                    $projectModel = new Projects($this->em);
                    if ($projectModel->isMember($user->getid(), $project->getId())) {
                        if ($this->isAlereadyVoted($user->getId(), $item_id)) {
                            $vote = $this->em->getRepository('App:ActivityVote')->findOneBy(array('idUser' => $user->getId(), 'idItem' => $item_id));
                            $vote->setType($type);
                            $this->em->flush();
                        } else {
                            $vote = new ActivityVote();
                            $vote->setIdItem($item_id);
                            $vote->setIdUser($user->getId());
                            $vote->setType($type);
                            $this->em->persist($vote);
                            $this->em->flush();
                        }
                        $obj = $this->updatesVotes($item_id);
                    } else {
                        $obj->state = 2;
                        $obj->error = "Vous n'êtes pas membre de ce projet vous ne pouvez donc pas voter";
                    }
                }
            }
        }
        return $obj;
    }

    function countActivity(int $projecId): int
    {
        $array = $this->em->getRepository('App:Activity')->findBy(array('project_id' => $projecId));
        return count($array);
    }

    function isAlereadyVoted(int $userId, int $itemId): bool
    {
        $item = $this->em->getRepository('App:ActivityVote')->findOneBy(array('idUser' => $userId, 'idItem' => $itemId));
        return $item != null;
    }

    function updatesVotes($item_id): \stdClass
    {
        $obj = new \stdClass();
        $votes_up = $this->em->getRepository('App:ActivityVote')->findBy(array('idItem' => $item_id, 'type' => 1));
        $votes_down = $this->em->getRepository('App:ActivityVote')->findBy(array('idItem' => $item_id, 'type' => 2));
        $activity = $this->em->getRepository('App:Activity')->find($item_id);
        $activity->setVotesUp(count($votes_up));
        $activity->setVotesDown(count($votes_down));
        $this->em->flush();
        $obj->state = 0;
        $obj->votes_up = count($votes_up);
        $obj->votes_down = count($votes_down);
        $obj->activity = $item_id;
        return $obj;
    }
}