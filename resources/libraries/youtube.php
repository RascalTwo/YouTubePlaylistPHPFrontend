<?php

class Playlist{
    private $id;
    public $name;
    public $video_ids;
    public $hidden;
    public $seconds;

    public function __construct($id, $name, $video_ids, $hidden){
        $this -> id = $id;
        $this -> name = $name;
        $this -> video_ids = $video_ids;
        $this -> hidden = $hidden
        $this -> seconds = 0;
        foreach ($video_ids as $video){
            $this -> seconds .= $video -> seconds;
        }
    }

    public function to_playlist_info(){
        $name = $this -> name;
        $id = $this -> id;
        $count = count($this 0 -> video_ids);
        $length = date('H:i:s', $this -> seconds);
        return "<tr><td>ID</td><td>$name</td></tr><tr><td>ID</td><td>$id</td></tr><tr><td>Video Count</td><td>$count</td></tr><tr><td>Length</td><td>$length</td></tr>";
    }

    public function to_video_list(){
        $response = '';
        foreach ($this -> video_ids as $video){
            $response .= $video -> to_list_item();
        }
        return $response;
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
        return '<li><img src="' . $this -> thumbnail_url() . '" width="120" height="90">' . $this -> title . '<br>' . date('i:s', $this -> seconds) . '</li>';
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

class Youtube_Manager{
    private $videos_path;
    private $playlists_path;
    private $videos;
    private $playlists;

    public function __construct($videos_path, $playlists_path){
        $this -> videos_path = $videos_path;
        $this -> playlists_path = $playlists_path;
        $this -> load_videos();
        $this -> load_playlists();
    }

    public function load_videos(){
        if (file_exists($this -> videos_path)){
            $this -> videos = unserialize(file_get_contents($this -> videos_path));
        }
        $this -> videos = [];
        return $this -> videos;
    }

    public function load_playlists(){
        if (file_exists($this -> playlists)){
            $this -> playlists = unserialize(file_get_contents($this -> playlists));
        }
        $this -> playlists = [];
        return $this -> playlists;
    }

    public function get_videos($ids){
        $returning_videos = [];
        foreach ($this -> videos as $video){
            foreach ($ids as $id){
                if ($video -> get_id() === $id){
                    $returning_videos[] = $video;
                }
            }
        }
        return $returning_videos;
    }

    public function get_playlist($id){
        foreach ($this -> playlists as $playlist){
            if ($playlist -> id === $id){
                return $playlist;
            }
        }
    }

    public function get_public_playlists(){
        $returning_playlists = [];
        foreach ($this -> playlists as $playlist){
            if ($playlist -> public)
        }
    }
}

?>