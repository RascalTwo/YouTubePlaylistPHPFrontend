<?php
class IDManager{
    private $path;

    public function __construct($path){
        $this -> load_ids();
    }

    private function load_ids(){
        if (file_exists($this -> path)){
            $this -> ids = unserialize(file_get_contents($path));
            return;
        }
        $this -> ids = [];
    }

    private function save_ids(){
        file_put_contents($this -> path, serialize($this -> ids));
    }

    public function generate_id(){
        $generated_id = rand(100000, 999999);
        while (in_array($generated_id, $this -> ids)){
            $generated_id = rand(100000, 999999);
        }
        $this -> ids[] = $generated_id;
        return $generated_id;
    }
}
?>