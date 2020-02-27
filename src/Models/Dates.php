<?php


namespace Models;


use Doctrine\Persistence\ObjectManager;

class Dates
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }


}