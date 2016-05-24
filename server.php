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

$router -> post("/api/youtube_playlist/get_playlist", function(){
    global $config;
    $json = [
        "message" => "",
        "playlist" => []
    ];
    $found_playlist;
    switch ($_POST["by"]) {
        case "id":
            $found_playlist = get_playlist_by_id($_POST["id"], $config["database"]["playlists"]);
            break;
        case "name":
            $found_playlist = get_playlist_by_name($_POST["name"], $config["database"]["playlists"]);
            break;
    }
    if (count($found_playlist) === 0){
        $json["message"] = "Playlist not found.";
        $json["status"] = 404;
    }
    else{
        $json["message"] = "Playlist Loaded";
        $json["status"] = 200;
        $json["playlist"] = $found_playlist -> to_array();
        $json["videos_list_html"] = $found_playlist -> to_video_list($config["database"]["videos"]);
    }
    echo json_encode($json);
}, ["by" => true, "id" => false, "name" => false]);

$router -> get("/api/youtube_playlist/playlists", function(){
    global $config;
    $response = "";
    foreach (get_public_playlists($config["database"]["playlists"]) as $playlist){
        $response .= $playlist -> to_list_item();
    }
    echo $response;
});

$router -> post("/api/youtube_playlist/add_video", function(){
    error_log(print_r($_POST, true));
    $videos = load_data($config["database"]["videos"]);
    if ($_POST["type"] === "list"){
        $new_videos = youtube_playlist_to_videos($_POST["id"]);
    }
    else{
        $new_videos = [new Video("https://www.youtube.com/watch?v=" . $_POST["id"])];
    }
    add_objects($new_videos, $config["database"]["videos"], $videos);

    $playlists = load_data($config["database"]["playlists"]);
    foreach ($playlists as $key => $_){
        if ($playlists[$key] -> get_id() != $_POST["id"]){
            continue;
        }
        $playlists[$key] -> add_videos($new_videos, $config["database"]["videos"]);
    }
    save_data($playlists, $config["database"]["playlists"]);
}, ["playlist" => true, "type" => true, "id" => true]);

$router -> post("/api/youtube_playlist/remove_video", function(){
    error_log(print_r($_POST, true));

    $playlists = load_data($config["database"]["playlists"]);
    foreach ($playlists as $key => $_){
        if ($playlists[$key] -> get_id() != $_POST["id"]){
            continue;
        }
        $playlists[$key] -> remove_videos($new_videos, $config["database"]["videos"]);
    }
    save_data($playlists, $config["database"]["playlists"]);

    if (!(video_in_playlist($_POST["id"], $playlists))){
        remove_objects([$_POST["id"]], $config["database"]["videos"]);
    }
}, ["playlist" => true, "id" => true]);

$router -> post("/api/youtube_playlist/add", function(){
    global $config;
    $playlists = load_data($config["database"]["playlists"]);
    if (count(get_playlist_by_name($_POST["name"], $playlists)) !== 0){
        echo json_encode([
            "message" => "Playlist name already exists."
        ]);
        return;
    }
    $playlist = new Playlist(generate_id($config["database"]["ids"]), $_POST["name"], $_POST["hidden"] === "true");
    add_objects([$playlist], $config["database"]["playlists"], $playlists);
    echo json_encode([
        "message" => "Playlist created."
    ]);
}, ["name" => true, "hidden" => true]);

$router -> match();

?>