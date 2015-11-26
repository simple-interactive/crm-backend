<?php

class App_Map_Menu extends Mongostar_Map_Instance {

    public static function rulesGetOne(){
        return [
            'id' => 'id',
            'image' => 'image',
            'name' => 'name'
        ];
    }

}