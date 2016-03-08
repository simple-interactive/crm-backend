<?php
/**
 * @class App_Model_STProduct
 *
 * @property MongoId $id
 * @property string  $productId
 * @property string  $sectionId
 * @property string  $orderId
 * @property string  $title
 * @property array   $ingredients
 * @property array   $options
 * @property float   $price
 * @property integer $weight
 * @property \MongoDate $createdAt
 *
 * @method static App_Model_STProduct [] fetchAll(array $cond = null, array $sort = null, $count = null, $offset = null, $hint = NULL)
 * @method static App_Model_STProduct|null fetchOne(array $cond = null, array $sort = null)
 * @method static App_Model_STProduct fetchObject(array $cond = null, array $sort = null)
 */
class App_Model_STProduct extends Mongostar_Model
{

}