<?php

/**
 * @property MongoId    $id
 * @property string     $email
 * @property string     $password
 * @property array      $tokens
 *
 * @method static App_Model_User[] fetchAll(array $cond = null, array $sort = null, $count = null, $offset = null, $hint = NULL)
 * @method static App_Model_User|null fetchOne(array $cond = null, array $sort = null)
 * @method static App_Model_User fetchObject(array $cond = null, array $sort = null)
 */
class App_Model_User extends Mongostar_Model
{
}