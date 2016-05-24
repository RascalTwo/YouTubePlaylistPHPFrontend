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

    private function calculate_length($path){
        $this -> seconds = 0;
        foreach ($this -> get_videos($path) as $video){
            $this -> seconds .= $video -> seconds;
        }
    }

    private function get_videos($path){
        return get_videos($this -> video_ids, $path);
    }

    public function get_id(){
        return $this -> id;
    }

    public function add_videos($ids, $path){
        array_merge($this -> video_ids, $ids);
        $this -> calculate_length($path);
    }

    public function remove_videos($ids, $path){
        foreach ($this -> video_ids as $key => $id){
            foreach ($ids as $removing_id){
                if ($id === $removing_id){
                    unset($this -> video_ids[$key]);
                }
            }
        }
        $this -> calculate_length($path);
    }

    public function to_playlist_info(){
        $name = $this -> name;
        $id = $this -> id;
        $count = count($this -> video_ids);
        $length = date('H:i:s', $this -> seconds);
        return "<tr><td>ID</td><td>$name</td></tr><tr><td>ID</td><td>$id</td></tr><tr><td>Video Count</td><td>$count</td></tr><tr><td>Length</td><td>$length</td></tr>";
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
        return '<li class="handy playlist" id="' . $this -> id . '">' . $image . $this -> name . '<br>' . count($this -> video_ids) . " Videos<br>" . date('H:i:s', $this -> seconds) . '</li>';
    }

    public function to_array(){
        return [
            "id" => $this -> id,
            "name" => $this -> name,
            "hidden" => $this -> hidden,
            "length" => date('H:i:s', $this -> seconds),
            "video_ids" => $this -> video_ids
        ];
    }
}

class Video{
    private $id;
    public $title;
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

function get_videos($ids, $videos){
    if ((!is_array($videos))){
        $videos = load_data($videos);
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

function youtube_playlist_to_videos($id){
    $html = file_get_contents("https://www.youtube.com/playlist?list=" . $id);
    $html = explode('class="pl-video yt-uix-tile "', $html);
    $videos = [];
    foreach ($html as $video_html){
        $videos[] = new Video("https://www.youtube.com/watch?v=" . explode('"', explode('data-video-id="', $video_html)[1])[0]);
    }
    return $videos;
}

function get_playlist_by_id($id, $playlists){
    if ((!is_array($playlists))){
        $playlists = load_data($playlists);
    }
    foreach ($playlists as $playlist){
        if ($playlist -> get_id() == $id){
            return $playlist;
        }
    }
}

function get_playlist_by_name($name, $playlists){
    if ((!is_array($playlists))){
        $playlists = load_data($playlists);
    }
    foreach ($playlists as $playlist){
        if (strtolower($playlist -> name) === strtolower($name)){
            return $playlist;
        }
    }
}

function get_public_playlists($playlists){
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

function video_in_playlist($id, $playlists){
    if ((!is_array($playlists))){
        $playlists = load_data($playlists);
    }
    foreach ($playlists as $playlist){
        if (array_key_exists($id, $playlist -> video_ids)){
            return true;
        }
    }
    return false;
}

function add_objects($objects, $path, $data=NULL){
    if ((!is_array($data))){
        $data = load_data($path);
    }
    foreach ($objects as $object){
        $data[] = $object;
    }
    save_data($data, $path);
}

function remove_objects($objects, $path, $data=NULL){
    if ((!is_array($data))){
        $data = load_data($path);
    }
    foreach ($data as $data_key => $_){
        foreach ($objects as $object_key => $value){
            if (is_object($value)){
                if ($data[$data_key] -> get_id() !== $objects[$object_key] -> get_id()){
                    continue;
                }
            }
            else{
                if ($data[$data_key] -> get_id() != $value){
                    continue;
                }
            }
            unset($data[$data_key]);
        }
    }
    save_data($data, $path);
}

?>