<?php

/**
 * Class STSection
 */
class App_Map_STSection extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'id' => 'id',
            'title' => 'title'
        ];
    }
}