<?php

    @session_start();
    
    require_once(dirname(__FILE__)."/fabrico/Fabrico.php");
    
    $fabrico = new Fabrico();
    $fabrico->injector()->inject(array("config", "controllers"), dirname(__FILE__)."/site");
    $fabrico->router()
        ->register("/games/add", "GamesAdd")
        ->register("/games/delete-player/@recordId", "PlayerDelete")
        ->register("/games/edit-player/@recordId", "PlayerEdit")
        ->register("/games/@idOfTheGame", "Game")
        ->register("/logout", "Logout")
        ->register("/money", "Money")
        ->register("/", "Games")
        ->run();
 
?>