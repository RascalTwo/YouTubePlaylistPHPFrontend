<?php
require_once __DIR__ . "/resources/config.php";
require_once $config["class"]["router"];
require_once $config["class"]["utility"];

$router = new Router($config["path"]["assets"]);
$id_manager = new IDManager($config["database"]["ids"]);

function show_template($template_path, $page_title, $spreadsheet=""){
    global $config;
    $template_path = $config["templates"][$template_path];
    include $config["templates"]["layout"];
}

$router -> get("/", function(){
    show_template("index", "Homepage");
    return;
});

$router -> get("/youtube_playlist", function(){
    show_template("youtube_playlist", "Youtube Playlists", "youtube_playlist");
    return;
});

$router -> get("/comics", function(){
    show_template("comics", "Comics");
    return;
});

$router -> post("/api/youtube_playlist/get", function(){
    return;
}, ["id" => true]);

$router -> match();

?>