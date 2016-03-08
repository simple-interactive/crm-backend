<?php

/**
 * Class STProduct
 */
class App_Map_STProduct extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'id' => 'id',
            'title' => 'title'
        ];
    }
}