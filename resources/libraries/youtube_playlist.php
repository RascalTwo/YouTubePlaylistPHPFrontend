<?php

class Youtube_Playlist{
    private $id;
    public $name;
    public $videos;

    public $total_length;

    public function __construct($id, $name, $videos){
        $this -> id = $id;
        $this -> name = $name;
        $this -> videos = $videos;
        $this -> total_length = new Date(0)
        foreach ($videos as $video){
            $this -> total_length .= $video -> length;
        }
    }
}

class Video{
    private $id;
    private $name;
    private $length;

    public function __construct($url){
        $html = file_get_contents($url);
    }

    public function length_string(){
        $minutes = floor($this -> length / 60);
        $seconds = $this -> length - minutes;
        return $minutes . ":" . $seconds;
    }

    public function to_list_item(){
        return '<li><img src="https://i.ytimg.com/vi/' . $this -> id . '/hqdefault.jpg" width="120" height="90">' . $this -> title . '<br>' . $this -> length_string() . '</li>';
    }
}

?>