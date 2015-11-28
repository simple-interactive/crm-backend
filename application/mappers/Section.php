<?php

class App_Map_Section extends Mongostar_Map_Instance
{
    public static function rulesCommon()
    {
        return [
            'id' => 'id',
            'parentId' => 'parentId',
            'title' => 'title',
            'image' => 'image'
        ];
    }
} 