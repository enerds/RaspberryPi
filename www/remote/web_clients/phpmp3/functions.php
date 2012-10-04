<?php

function dump() {
    $arg_list = func_get_args();
    echo '<pre>';
    foreach ($arg_list as $v) {
        var_dump($v); echo "\n";
    }
    echo '</pre>';
}


function postget($var){

    if (isset($_POST[$var])) {
        $res = $_POST[$var];
    }
    elseif (isset($_GET[$var])) {
        $res = $_GET[$var];
    }
    else $res = false;

    return $res;

}


?>