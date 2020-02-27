<?php


namespace Models;


use App\Entity\HotelVote;
use Config\Premium_Config;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class Hotel
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function addHotel(Request $request, $token): \stdClass
    {
        $obj = new \stdClass();
        $nom = $request->get('hName');
        $duration = $request->get('hDuration');
        $durationValue = $request->get('hDurationParam');
        $price = $request->get('hPrice');
        $description = $request->get('hDescription');
        $number = $request->get('hNumber');
        $street = $request->get('hStreet');
        $zip = $request->get('hZip');
        $city = $request->get('hCity');
        $project_id = $request->get('hProject');

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
                } else if ($this->countHotel($project_id) < $premium->max_hotel) {
                    $flag = true;
                }
                if ($flag) {
                    $mapApi = new MapApi();
                    $address_api = $number . "+" . $street . "+" . $city . "+" . $zip;
                    $adresse = $number . " " . $street . " " . $city . " " . $zip;
                    $location = $mapApi->getLocation($address_api);
                    $hotel = new \App\Entity\Hotel();
                    $hotel->setNom($nom);
                    $hotel->setDuration($duration);
                    $hotel->setDurationParam($durationValue);
                    $hotel->setPrice($price);
                    $hotel->setDescription($description);
                    $hotel->setLat($location->latitude);
                    $hotel->setLongitude($location->longitude);
                    $hotel->setAddress($adresse);
                    $hotel->setUserId($user->getId());
                    $hotel->setVotesUp(0);
                    $hotel->setVotesDown(0);
                    $hotel->setState(0);
                    $hotel->setProjectId($project_id);
                    $this->em->persist($hotel);
                    $this->em->flush();
                    $obj->state = 0;
                    $json = new \stdClass();
                    $json->id = $hotel->getId();
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
                    $obj->hotel = json_encode($json);
                } else {
                    $obj->state = 3;
                    $obj->error = "Limite d'hotel atteinte envie de plus ? passez à la version premium";
                }

            } else {
                $obj->state = 2;
                $obj->error = "Vous n'êtes pas membre de ce projet vous ne pouvez donc pas soumettre d'activités ou d'hotels";
            }

        }
        return $obj;
    }

    public function getHotel($projectId): array
    {
        $aHotel = array();
        $project = $this->em->getRepository('App:Project')->find($projectId);
        if ($project != null) {
            $hotels = $this->em->getRepository('App:Hotel')->findBy(array('project_id' => $projectId));
            foreach ($hotels as $item):
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
                array_push($aHotel, $json);
            endforeach;
        }
        return $aHotel;
    }

    function countHotel(int $projecId): int
    {
        $array = $this->em->getRepository('App:Hotel')->findBy(array('project_id' => $projecId));
        return count($array);
    }

    public function vote(Request $request, $token): \stdClass
    {
        $obj = new \stdClass();
        $type = $request->get('type');
        $item_id = $request->get('item_id');
        if (empty($type) OR empty($item_id)) {
            $obj->state = 3;
            $obj->error = "Des paramètres sont manquants dans la requête de vote";
        } else {
            $hotel = $this->em->getRepository('App:Hotel')->find($item_id);
            if ($hotel == null) {
                $obj->state = 2;
                $obj->error = "L'hotel n'existe pas";
            } else {
                $project = $this->em->getRepository('App:Project')->find($hotel->getProjectId());
                if ($project == null) {
                    $obj->state = 2;
                    $obj->error = "Le projet n'existe pas";
                } else {
                    $auth = new AuthModel();
                    $user = $this->em->getRepository('App:Users')->findOneBy(array('email' => $auth->getTokenInfo($token)));
                    $projectModel = new Projects($this->em);
                    if ($projectModel->isMember($user->getid(), $project->getId())) {
                        if ($this->isAlereadyVoted($user->getId(), $item_id)) {
                            $vote = $this->em->getRepository('App:HotelVote')->findOneBy(array('idUser' => $user->getId(), 'idItem' => $item_id));
                            $vote->setType($type);
                            $this->em->flush();
                        } else {
                            $vote = new HotelVote();
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

    function isAlereadyVoted(int $userId, int $itemId): bool
    {
        $item = $this->em->getRepository('App:HotelVote')->findOneBy(array('idUser' => $userId, 'idItem' => $itemId));
        return $item != null;
    }

    function updatesVotes($item_id): \stdClass
    {
        $obj = new \stdClass();
        $votes_up = $this->em->getRepository('App:HotelVote')->findBy(array('idItem' => $item_id, 'type' => 1));
        $votes_down = $this->em->getRepository('App:HotelVote')->findBy(array('idItem' => $item_id, 'type' => 2));
        $hotel = $this->em->getRepository('App:Hotel')->find($item_id);
        $hotel->setVotesUp(count($votes_up));
        $hotel->setVotesDown(count($votes_down));
        $this->em->flush();
        $obj->state = 0;
        $obj->votes_up = count($votes_up);
        $obj->votes_down = count($votes_down);
        $obj->hotel = $item_id;
        return $obj;
    }
}