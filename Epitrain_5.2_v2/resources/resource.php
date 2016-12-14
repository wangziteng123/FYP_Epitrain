<?php
    class resource {
        var $resource_id;
        var $resource_type;
        function $is_equal($resource){
            if ($this->resource_id === $resource->resource_id) {
                return true;
            }
            return false;
        }
        function set_resource_id($id) {
        	$this->resource_id = $id;
        }
        function get_resource_id() {
        	return $this->resource_id;
        }

        function set_resource_type($type){
            $this->resource_type = $type;
        }
        function get_type(){
            return &this->resource_type;
        }
    }

    class book extends resource {
        var $name;
        var $isbn;
        var $author;
        var $profession;
        var $description;

        function set_name($name){
            $this->name = $name;
        }
        function set_isbn($isbn){
            $this->isbn = $isbn;
        }
        function set_author($author){
            $this->author = $author;
        }
        function set_profession($profession){
            $this->profession = $profession;
        }
        function set_description($description){
            $this->description = $description;
        }


        function get_name()[
            return $this->name;
        }
        function get-isbn(){
            return $this->isbn;
        }
        function get_author(){
            return $this->author;
        }
        funciton get_profession(){
            return $this->profession;
        }
        funciton get_description(){
            return $this->description;
        }
    }

    class spreadsheet extends resource {

    }

    class discussion extends resource {
        var $title;
        var $content;
        var $time;
        var $tags;

        function set_title($title){
            $this->title = $title;
        }
        function set_content($content){
            $this->content = $content;
        }
        function set_time($time){
            $this->time = $time;
        }
        function set_tags($tags){
            $this->tags = $tags;
        }

        function get_title(){
            return $this->title;
        }
        function get_content(){
            return $this->content;
        }
        function get_time(){
            return $this->time;
        }
        function get_tags(){
            return $this->tags;
        }
    }
?>