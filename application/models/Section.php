<?php

/**
 * @property MongoId $id
 * @property string $userId
 * @property array $image
 * @property string $title
 * @property string $parentId
 *
 * @method static App_Model_Section[] fetchAll(array $cond = null, array $sort = null, $count = null, $offset = null, $hint = NULL)
 * @method static App_Model_Section|null fetchOne(array $cond = null, array $sort = null)
 * @method static App_Model_Section fetchObject(array $cond = null, array $sort = null)
 */
class App_Model_Section extends Mongostar_Model
{
}