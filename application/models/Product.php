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
 * @property array   $options
 * @property bool    $exists
 * @property string  $hashForSt
 *
 * @method static App_Model_Product[] fetchAll(array $cond = null, array $sort = null, $count = null, $offset = null, $hint = NULL)
 * @method static App_Model_Product|null fetchOne(array $cond = null, array $sort = null)
 * @method static App_Model_Product fetchObject(array $cond = null, array $sort = null)
 */
class App_Model_Product extends Mongostar_Model
{
    public function save()
    {
        $forHash = [
            $this->title,
            $this->sectionId,
            $this->price,
            $this->weight,
            $this->ingredients,
            $this->options
        ];

        $this->hashForSt = sha1(print_R($forHash, true));
        return parent::save();
    }

} 