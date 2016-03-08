<?php

/**
 * Class STIngredient
 */
class App_Map_STIngredient extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'id' => 'id',
            'title' => 'title'
        ];
    }
}