<?php

// On enregistre notre autoload
    function chargerClasse($classname)
    {
        require './class/'.$classname.'.class.php';
    }
    
    spl_autoload_register('chargerClasse');
