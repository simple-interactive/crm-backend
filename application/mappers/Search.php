<?php

class App_Map_Search extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'id' => 'id',
            'productId' => 'productId',
            'data' => 'data'
        ];
    }
}