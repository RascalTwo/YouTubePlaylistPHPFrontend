<?php

function generate_id($path){
    $ids;
    if (file_exists($path)){
        $ids = unserialize(file_get_contents($path));
    }
    else{
        $ids = [];
    }
    $generated_id = rand(1000, 9999);
    while (in_array($generated_id, $ids)){
        $generated_id = rand(1000, 9999);
    }
    $ids[] = $generated_id;
    file_put_contents($path, serialize($ids));
    return $generated_id;
}

?>