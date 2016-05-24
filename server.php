<?php
require_once __DIR__ . "/resources/config.php";
require_once $config["class"]["router"];
require_once $config["class"]["utility"];
require_once $config["class"]["youtube"];

$router = new Router($config["path"]["assets"]);

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
}, ["name" => true]);

$router -> get("/api/youtube_playlist/playlists", function(){
    global $config;
    $response = "";
    foreach (get_public_playlists($config["database"]["playlists"]) as $playlist){
        $response .= $playlist -> to_list_item();
    }
    echo $response;
});

$router -> post("/api/youtube_playlist/add", function(){
    global $config;
    $playlists = load_data($config["database"]["playlists"]);
    $playlist = new Playlist(generate_id($config["database"]["ids"]), $_POST["name"], $_POST["hidden"] === "true");
    add_object($playlist, $config["database"]["playlists"], $playlists);
    echo $playlist -> to_json();
}, ["name" => true, "hidden" => true]);

$router -> match();

?>