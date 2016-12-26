<?php

$modules = array(
    'home' => array(
        'route' => '/'
            ),
);


foreach ($modules as $name=>$option) {
    echo $option["route"];
}