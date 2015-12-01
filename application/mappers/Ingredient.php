<?php

/**
 * @class App_Map_Ingredient
 */
class App_Map_Ingredient extends Mongostar_Map_Instance
{
    /**
     * @return array
     */
    public static function rulesCommon()
    {
        return [
            'id' => 'id',
            'title' => 'title'
        ];
    }
} 