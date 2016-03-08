<?php
/**
 * @class App_Model_Order
 *
 * @property MongoId $id
 * @property array   $data
 * @property string  $status
 * @property integer $createdDate
 * @property string  $payStatus
 * @property string  $paymentMethod
 * @property string  $tableId
 * @property string  $userId
 *
 * @method static App_Model_Order[] fetchAll(array $cond = null, array $sort = null, $count = null, $offset = null, $hint = NULL)
 * @method static App_Model_Order|null fetchOne(array $cond = null, array $sort = null)
 * @method static App_Model_Order fetchObject(array $cond = null, array $sort = null)
 */
class App_Model_Order extends Mongostar_Model
{

}