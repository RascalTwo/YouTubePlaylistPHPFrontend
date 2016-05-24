<?php
$DOCUMENT_ROOT = realpath(__DIR__ . "/..");
$RESOURCES = realpath(__DIR__ . "/../resources");

$config = [
    "class" => [
        "router" => $RESOURCES . "/libraries/router.php",
        "youtube" => $RESOURCES . "/libraries/youtube.php",
        "utility" => $RESOURCES . "/libraries/utility.php"
    ],
    "database" => [
        "ids" => $RESOURCES . "/database/ids.db",
        "videos" => $RESOURCES . "/database/videos.db",
        "playlists" => $RESOURCES . "/database/playlists.db"
    ],
    "path" => [
        "assets" => $DOCUMENT_ROOT . "/assets"
    ],
    "templates" => [
        "index" => $RESOURCES . "/templates/index.php",
        "layout" => $RESOURCES . "/templates/layout.php",
        "youtube_playlist" => $RESOURCES . "/templates/youtube_playlist.php",
        "comics" => $RESOURCES . "/templates/comics.php"
    ]
];

foreach ($config["database"] as $database){
    if (!file_exists($database)){
        file_put_contents($database, serialize([]));
    }
}

unset($RESOURCES);
unset($DOCUMENT_ROOT);

?>