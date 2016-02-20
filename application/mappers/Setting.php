<?php

class App_Map_Setting extends Mongostar_Map_Instance
{
    public function rulesCommon()
    {
        return [
            'id' => 'id',
            'userId' => 'userId',
            'liqpay' => 'liqpay'
        ];
    }
}