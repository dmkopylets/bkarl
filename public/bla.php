<?php
$jsonfile = file_get_contents("http://api.geonames.org/searchJSON?username=ksuhiyp&country=ua&maxRows=100&style=SHORT&lang=uk");

echo $jsonfile;

// $geodat = json_decode($jsonfile,true);
// $names = array();
// foreach($geodat['geonames'] as $geoname) {
//     $names[] = $geoname['name'];
// }
// print_r($names);