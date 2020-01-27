<?php

namespace App\Controller;

use App\Entity\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends AbstractController
{
    /**
     * @Route("/test/{name}", name="hello-world", requirements={"name"= "\d"}, defaults={"name"=1})
     */
    public function index($name)
    {
        $em = $this->getDoctrine()->getManager();
        $test = $em->getRepository('App:Config')->find($name);
        if ($test == null) {
            return new Response("NOP");
        } else {
            //$data['txt'] = $test->getCgv();
            $obj = new \stdClass();
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository('App:Users')->findAll();
            $count = count($users);
            $obj->user = $count;
            $obj->project = 0;
            $obj->subtitle = "Voter voyage commence maintenant";
            $obj->cafe = $test->getCafe();
            $obj->cgu = $test->getCgu();
            $obj->cgv = $test->getCgv();
            $obj->rgpd = $test->getRgpd();
            $data['txt'] = json_encode($obj);
            return new JsonResponse($obj, JsonResponse::HTTP_OK);
        }
    }

    /**
     * @Route("/cafe", name="cafe")
     */
    public function addCofee()
    {

        $em = $this->getDoctrine()->getManager();
        $test = $em->getRepository('App:Config')->find(1);
        $cofee = $test->getCafe();
        $test->setCafe($cofee + 1);
        $em->flush();

        return $this->redirectToRoute('hello-world');
    }


    /**
     * @Route("/add", name="add")
     */
    public function createTest()
    {
        $config = new Config();
        $config->setCafe(0);
        $config->setCgu("Les cgu ça craint");
        $config->setCgv("J'bicrave du shit pas besoin de ça wsh");
        $config->setRgpd("Je te connais tu me connais tu parle je te frappe");

        $em = $this->getDoctrine()->getManager();

        $em->persist($config);

        $em->flush();

        return $this->redirectToRoute('hello-world', array('name' => $config->getId()));
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id" = "\d"})
     */
    public function deleteTest($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('App:Config')->find($id);
        if ($config == null) {
            return new Response("NOP");
        } else {
            if ($id != 1) {
                $em->remove($config);
                $em->flush();
            }
            return $this->redirectToRoute('hello-world', array('name' => $id));
        }
    }

}

