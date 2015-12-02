<?php

/**
 * @class App_Map_Product
 */
class App_Map_Product extends Mongostar_Map_Instance
{
    public static function rulesCommon()
    {
        return [
            'id' => 'id',
            'sectionId' => 'sectionId',
            'title' => 'title',
            'description' => 'description',
            'price' => 'price',
            'weight' => 'weight',
            'images' => 'images',
            'ingredients' => 'ingredients',
            'exists' => 'exists'
        ];
    }
} 