<?php

$error = [];
if($name == ''){
    $error['naam'] = 'U moet een naam invullen';
}

if($location == ''){
    $error['locatie'] = 'U moet een locatie invullen';
}

if($date == ''){
    $error['datum'] = 'U moet een datum invullen';
}

if($time == '') {
    $error['tijd'] = 'U moet een tijd invullen';
} else if(substr($time, 0 ,2) < 9 || substr($time, 0, 2) >= 17) {
        $error['tijd'] = 'U kunt op deze dag alleen tussen 09:00 en 17:00 een afspraak maken.';
}

if(strtotime($datetime) <= time()){
    $error['datum'] = 'U kunt geen afspraak maken in het verleden';
}