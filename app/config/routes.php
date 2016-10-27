<?php

$router = new \Phalcon\Mvc\Router();

$router->addGet("/api/empty", array(       
    'controller' => 'Vote',
    'action' => 'dummy',
));

$router->addPost("/api/empty", array(       
    'controller' => 'Vote',
    'action' => 'dummy',
));

$router->addPost("/api/vote", array(       
    'controller' => 'Vote',
    'action' => 'vote',
));

$router->addGet("/api/vote-status/{userid}", array(       
    'controller' => 'Vote',
    'action' => 'getVoteStatus',
));

$router->addGet("/api/vote-counts/{postid}", array(       
    'controller' => 'Vote',
    'action' => 'getVoteCounts',
));


return $router;