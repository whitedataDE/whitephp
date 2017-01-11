<?php

$modules = array(
    'tasks' => array(
        'route' => '/'
            ),
   'api'    => array(
       'route' => '/api',
       'version-controlled' => true,
   ),
);


echo json_encode($modules);