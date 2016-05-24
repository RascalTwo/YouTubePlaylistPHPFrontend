<?php

class Playlist{
    private $id;
    public $name;
    public $video_ids;
    public $hidden;
    public $seconds;

    public function __construct($id, $name, $hidden){
        $this -> id = $id;
        $this -> name = $name;
        $this -> video_ids = [];
        $this -> hidden = $hidden;
        $this -> seconds = 0;

        foreach ($this -> video_ids as $video){
            $this -> seconds .= $video -> seconds;
        }
    }

    private function calculate_length(){
        $this -> seconds = 0;
        foreach ($video_ids as $video){
            $this -> seconds .= $video -> seconds;
        }
    }

    public function add_videos($ids){
        array_merge($this -> video_ids, $ids);
        $this -> calculate_length();
    }

    public function remove_videos($ids){
        foreach ($this -> video_ids as $key => $id){
            foreach ($ids as $removing_id){
                if ($id === $removing_id){
                    unserialize($this -> video_ids[$key]);
                }
            }
        }
        $this -> calculate_length();
    }

    public function to_playlist_info(){
        $name = $this -> name;
        $id = $this -> id;
        $count = count($this -> video_ids);
        $length = date('H:i:s', $this -> seconds);
        return "<tr><td>ID</td><td>$name</td></tr><tr><td>ID</td><td>$id</td></tr><tr><td>Video Count</td><td>$count</td></tr><tr><td>Length</td><td>$length</td></tr>";
    }

    private function get_videos($path){
        return get_videos($this -> video_ids, $path);
    }

    public function to_video_list($path){
        $response = '';
        foreach ($this -> get_videos($path) as $video){
            $response .= $video -> to_list_item();
        }
        return $response;
    }

    public function to_list_item(){
        $image = "";
        if (count($this -> video_ids) !== 0){
            $image = '<img src="' . $this -> get_videos($path)[0] . '" width="120" height="90">';
        }
        return '<li id="' . $this -> id . '">' . $image . $this -> name . '<br>' . count($this -> video_ids) . " Videos<br>" . date('H:i:s', $this -> seconds) . '</li>';
    }

    public function to_json(){
        $json = [
            "id" => $this -> id,
            "name" => $this -> name,
            "hidden" => $this -> hidden,
            "length" => date('H:i:s', $this -> seconds),
            "video_ids" => $this -> video_ids
        ];
        return json_encode($json);
    }
}

class Video{
    private $id;
    private $title;
    private $seconds;

    public function __construct($url){
        $html = file_get_contents($url);
        $this -> id = explode('"', explode('<meta property="og:url" content="https://www.youtube.com/watch?v=', $html)[1])[0];
        $this -> title = explode('"', explode('<meta property="og:title" content="', $html)[1])[0];
        $this -> seconds = explode('"', explode('length_seconds":"', $html)[1])[0];
    }

    public function to_list_item(){
        return '<li id="' . $this -> id . '"><img src="' . $this -> thumbnail_url() . '" width="120" height="90">' . $this -> title . '<br>' . date('i:s', $this -> seconds) . '</li>';
    }

    public function thumbnail_url(){
        return "https://i.ytimg.com/vi/" . $this -> id . "/maxresdefault.jpg";
    }

    public function embed_url(){
        return "https://www.youtube.com/embed/" . $this -> id;
    }

    public function get_id(){
        return $this -> id;
    }
}

function load_data($path){
    $videos;
    if (file_exists($path)){
        $videos = unserialize(file_get_contents($path));
    }
    else{
        $videos = [];
    }
    return $videos;
}

function save_data($data, $path){
    file_put_contents($path, serialize($data));
}

function get_videos($ids, $videos=NULL){
    if ((!is_array($videos))){
        $videos = load_videos($videos);
    }
    $returning_videos = [];
    foreach ($videos as $video){
        foreach ($ids as $id){
            if ($video -> get_id() === $id){
                $returning_videos[] = $video;
            }
        }
    }
    return $returning_videos;
}

function get_playlist($id, $playlists=NULL){
    if ((!is_array($playlists))){
        $playlists = load_data($playlists);
    }
    foreach ($playlists as $playlist){
        if ($playlist -> id === $id){
            return $playlist;
        }
    }
}

function get_public_playlists($playlists=NULL){
    if ((!is_array($playlists))){
        $playlists = load_data($playlists);
    }
    $returning_playlists = [];
    foreach ($playlists as $playlist){
        if (!($playlist -> hidden)){
            $returning_playlists[] = $playlist;
        }
    }
    return $returning_playlists;
}

function add_object($object, $path, $data=NULL){
    if ((!is_array($data))){
        $data = load_data($path);
    }
    $data[] = $object;
    save_data($data, $path);
}

?>