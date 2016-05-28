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

function cleanup_videos($videos_path, $playlists_path){
    $playlists = load_data($playlists_path);
    $videos = load_data($videos_path);
    foreach ($videos as $key => $_){
        if (video_in_playlist($videos[$key] -> get_id(), $playlists)){
            continue;
        }
        unset($videos[$key]);
    }
    foreach ($videos as $key_one => $_){
        foreach ($videos as $key_two => $_){
            if ($key_one === $key_two){
                continue;
            }
            if ($videos[$key_one] -> get_id() !== $videos[$key_two] -> get_id()){
                continue;
            }
            unset($videos[$key_two]);
        }
    }
    save_data($videos, $videos_path);
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

$router -> get("/api/youtube_playlist/playlists", function(){
    global $config;
    $response = "";
    foreach (get_public_playlists($config["database"]["playlists"]) as $playlist){
        $response .= $playlist -> to_list_item($config["database"]["videos"]);
    }
    echo $response;
});

$router -> post("/api/youtube_playlist/get_playlist", function(){
    global $config;
    $json = [
        "message" => "",
        "playlist" => []
    ];

    $found_playlist = NULL;
    switch ($_POST["by"]) {
        case "id":
            $found_playlist = get_playlist_by_id($_POST["id"], $config["database"]["playlists"]);
            break;
        case "name":
            $found_playlist = get_playlist_by_name($_POST["name"], $config["database"]["playlists"]);
            break;
    }
    if ($found_playlist === NULL){
        $json["status"] = 404;
    }
    else{
        $json["status"] = 200;
        $json["playlist"] = $found_playlist -> to_array($config["database"]["videos"]);
    }

    echo json_encode($json);
}, ["by" => true, "id" => false, "name" => false]);

$router -> post("/api/youtube_playlist/add_video", function(){
    global $config;
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
        if ($playlists[$key] -> get_id() != $_POST["playlist"]){
            continue;
        }
        $playlists[$key] -> add_video_ids(objects_to_ids($new_videos), $config["database"]["videos"]);
    }
    save_data($playlists, $config["database"]["playlists"]);
}, ["playlist" => true, "type" => true, "id" => true]);

$router -> post("/api/youtube_playlist/remove_video", function(){
    global $config;
    $playlists = load_data($config["database"]["playlists"]);
    foreach ($playlists as $key => $_){
        if ($playlists[$key] -> get_id() != $_POST["playlist"]){
            continue;
        }
        $playlists[$key] -> remove_videos([$_POST["id"]], $config["database"]["videos"]);
    }
    save_data($playlists, $config["database"]["playlists"]);

    if (!(video_in_playlist($_POST["id"], $playlists))){
        remove_objects([$_POST["id"]], $config["database"]["videos"]);
    }
}, ["playlist" => true, "id" => true]);

$router -> post("/api/youtube_playlist/reorder_video", function(){
    global $config;
    $change = false;
    $playlists = load_data($config["database"]["playlists"]);
    foreach ($playlists as $key => $_){
        if ($playlists[$key] -> get_id() != $_POST["playlist"]){
            continue;
        }
        $change = $playlists[$key] -> reorder_video($_POST["id"], $_POST["direction"]);
    }
    save_data($playlists, $config["database"]["playlists"]);

    echo json_encode(["refresh" => $change]);
}, ["playlist" => true, "id" => true, "direction" => true]);

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

$router -> post("/api/youtube_playlist/edit_playlist", function(){
    global $config;
    $playlists = load_data($config["database"]["playlists"]);
    foreach ($playlists as $key => $_){
        if ($playlists[$key] -> get_id() != $_POST["playlist"]){
            continue;
        }
        foreach ($_POST as $post_key => $value){
            if (property_exists($playlists[$key], $post_key)){
                $playlists[$key] -> {$post_key} = $value;
            }
        }
    }
    save_data($playlists, $config["database"]["playlists"]);
}, ["playlist" => true, "shuffle" => false, "hidden" => false, "name" => false]);

$router -> post("/api/youtube_playlist/edit_video", function(){
    global $config;
    $change = false;
    $videos = load_data($config["database"]["videos"]);
    foreach ($videos as $key => $_){
        if ($videos[$key] -> get_id() != $_POST["id"]){
            continue;
        }
        foreach ($_POST as $post_key => $value){
            if ($post_key === "id"){
                continue;
            }
            if (property_exists($videos[$key], $post_key)){
                if ($videos[$key] -> {$post_key} !== $value){
                    $change = true;
                }
                $videos[$key] -> {$post_key} = $value;
            }
        }
    }
    save_data($videos, $config["database"]["videos"]);

    echo json_encode(["refresh" => $change]);
}, ["id" => true, "volume" => true, "title" => true]);

cleanup_videos($config["database"]["videos"], $config["database"]["playlists"]);

$router -> match();

?>