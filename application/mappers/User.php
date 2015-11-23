<?php

class App_Map_User extends Mongostar_Map_Instance
{
    /**
     * @return array
     */
    public function rulesAuth()
    {
        return [
            'id' => 'id',
            'email' => 'email',
            'token' => 'token'
        ];
    }

    /**
     * @param App_Model_User $user
     * @return string
     */
    public function getToken(App_Model_User $user)
    {
        $tokens = $user->tokens;
        return end($tokens);
    }
}