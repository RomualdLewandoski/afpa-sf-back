<?php


namespace Models;


class MapApi
{
    public function getLocation($address): \stdClass
    {
        $obj = new \stdClass();
        $config = new \Config\MapApi();
        $key = $config->api_key;
        $address = str_replace(" ", "%20", $address);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://geocoder.ls.hereapi.com/6.2/geocode.json?apiKey=" . $key . "&searchtext=" . $address);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($output);
        $final = $json->Response->View[0]->Result[0]->Location->DisplayPosition;
        $obj->latitude = $final->Latitude;
        $obj->longitude = $final->Longitude;

        return $obj;
    }
}