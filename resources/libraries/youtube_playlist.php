<?php

class Youtube_Playlist{
    private $id;
    public $name;
    public $videos;
    public $public;
    public $seconds;

    public function __construct($id, $name, $videos){
        $this -> id = $id;
        $this -> name = $name;
        $this -> videos = $videos;
        $this -> seconds = new Date(0)
        foreach ($videos as $video){
            $this -> seconds .= $video -> seconds;
        }
    }

    public function to_table_rows(){
        $name = $this -> name;
        $count = count($this -> videos);
        $length = date('H:i:s', $this -> seconds);
        return "<tr><td>Name</td><td>$name</td></tr><tr><td>Videos</td><td>$count</td></tr><tr><td>Full Length</td><td>$length</td></tr>";
    }

    public function get_video_list(){
        $response = '';
        foreach ($this -> videos as $video){
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
}

?>