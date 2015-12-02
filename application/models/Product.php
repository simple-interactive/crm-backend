<?php

/**
 * @class App_Model_Product
 *
 * @property MongoId $id
 * @property string  $userId
 * @property string  $sectionId
 * @property string  $title
 * @property string  $description
 * @property float   $price
 * @property integer $weight
 * @property array   $images
 * @property array   $ingredients
 * @property bool    $exists
 *
 * @method static App_Model_Product[] fetchAll(array $cond = null, array $sort = null, $count = null, $offset = null, $hint = NULL)
 * @method static App_Model_Product|null fetchOne(array $cond = null, array $sort = null)
 * @method static App_Model_Product fetchObject(array $cond = null, array $sort = null)
 */
class App_Model_Product extends Mongostar_Model
{

} 