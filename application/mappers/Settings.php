<?php

class App_Map_Settings extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'userId' => 'userId',
            'data' => 'data'
        ];
    }
}