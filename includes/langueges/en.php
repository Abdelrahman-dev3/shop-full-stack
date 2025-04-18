<?php

function lang($phrase){

    static $lang = [
        "error_msg" => "not found this username",
        "main_title" => "welcom back",
        "discreption" => "let's go",
    ];

    return $lang[$phrase];

}

?>