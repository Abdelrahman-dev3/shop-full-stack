<?php

function lang($phrase){

    static $lang = [
        "title" => "المسئول",
        "main_title" => "اهلا بعودتك",
        "discreption" => "هيا بنا",
    ];

    return $lang[$phrase];

}

?>