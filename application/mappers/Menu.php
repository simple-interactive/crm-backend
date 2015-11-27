<?php

class App_Map_Menu extends Mongostar_Map_Instance {

    public static function rulesCommon(){
        return [
            'id' => 'id',
            'image' => 'image',
            'title' => 'title'
        ];
    }

}