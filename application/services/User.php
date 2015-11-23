<?php

class App_Service_User
{
    const E_WRONG_CREDENTIALS = 'wrong-credentials';

    /**
     * @var string
     */
    protected $_lastError = null;

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->_lastError;
    }

    /**
     * @param string $lastError
     */
    public function setLastError($lastError)
    {
        $this->_lastError = $lastError;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return App_Model_User|false
     */
    public function auth($email, $password)
    {
        if (!empty($email) && !empty($password)) {

            $user = App_Model_User::fetchOne([
                'email' => $email,
                'password' => md5($password)
            ]);
        }

        if (!empty($user)) {

            $tokens = (empty($user->tokens))?[]:$user->tokens;
            $tokens[] = md5(microtime());
            $user->tokens = $tokens;

            $user->save();

            return $user;
        }

        $this->setLastError(self::E_WRONG_CREDENTIALS);
        return false;
    }

    /**
     * @param App_Model_User $user
     */
    public function logout(App_Model_User $user)
    {
        $user->tokens = [];
        $user->save();
    }

    /**
     * @param string $token
     * @return App_Model_User|bool
     */
    public function identify($token)
    {
        if (!empty($token)) {

            $user = App_Model_User::fetchOne([
                'tokens' => $token
            ]);

            if ($user) {
                return $user;
            }
        }

        return false;
    }
}