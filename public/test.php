<?php
$var = "{\"Response\":{\"MetaInfo\":{\"Timestamp\":\"2020-02-14T10:18:28.302+0000\"},\"View\":[{\"_type\":\"SearchResultsViewType\",\"ViewId\":0,\"Result\":[{\"Relevance\":0.96,\"MatchLevel\":\"houseNumber\",\"MatchQuality\":{\"City\":1.0,\"Street\":[1.0],\"HouseNumber\":1.0,\"PostalCode\":0.85},\"MatchType\":\"pointAddress\",\"Location\":{\"LocationId\":\"NT_eWjcU79n.QYJe9NSYGquRB_xA\",\"LocationType\":\"point\",\"DisplayPosition\":{\"Latitude\":50.32241,\"Longitude\":3.20649},\"NavigationPosition\":[{\"Latitude\":50.32236,\"Longitude\":3.20655}],\"MapView\":{\"TopLeft\":{\"Latitude\":50.3235342,\"Longitude\":3.2047293},\"BottomRight\":{\"Latitude\":50.3212858,\"Longitude\":3.2082507}},\"Address\":{\"Label\":\"1 Rue d'Arras, 59234 Monchecourt, France\",\"Country\":\"FRA\",\"State\":\"Hauts-de-France\",\"County\":\"Nord\",\"City\":\"Monchecourt\",\"Street\":\"Rue d'Arras\",\"HouseNumber\":\"1\",\"PostalCode\":\"59234\",\"AdditionalData\":[{\"value\":\"France\",\"key\":\"CountryName\"},{\"value\":\"Hauts-de-France\",\"key\":\"StateName\"},{\"value\":\"Nord\",\"key\":\"CountyName\"}]}}}]}]}}";
$spacer = "<br><br><br><br>";

echo $var."<br><br><br>";

$json1 = json_decode($var);

$json2 = $json1->Response;

$json3 = $json2->View;

$json4 = $json3[0]->Result;

$json5 = $json4[0]->Location;

$json6 = $json5->DisplayPosition;
/*var_dump($json6);
echo $spacer;
echo $json6->Latitude." | ".$json6->Longitude;
echo $spacer;
$plop  = $json1->Response->View[0]->Result[0]->Location->DisplayPosition;
echo $spacer;
echo $plop->Latitude." | ".$plop->Longitude;*/
phpinfo();