<?php

class App_Service_Menu
{
    use App_Trait_ImageLoader;
    /**
     * @param App_Model_User $user
     * @param App_Model_Ingredient $ingredient
     * @param $title
     *
     * @return App_Model_Ingredient
     * @throws Exception
     */
    public function saveIngredient(
        App_Model_User $user,
        App_Model_Ingredient $ingredient = null,
        $title
    )
    {
        if (empty($title) || mb_strlen($title, 'UTF-8') < 2 || mb_strlen($title, 'UTF-8') > 20) {
            throw new Exception('ingredient-title-invalid', 400);
        }
        if (!$ingredient) {
            $ingredient = new App_Model_Ingredient();
            $ingredient->userId = (string) $user->id;
        }
        elseif ($ingredient->userId != (string) $user->id) {
            throw new Exception('permission-denied', 400);
        }

        if (App_Model_Ingredient::fetchOne([
            'userId' => (string) $user->id,
            'title' => trim($title)
        ])) {
           throw new Exception('already-exists', 400);
        }
        $ingredient->title = $title;
        $ingredient->save();
        return $ingredient;
    }

    /**
     * @var App_Model_User $user
     * @var string $search
     *
     * @throws Exception
     * @return App_Model_Ingredient[]
     */
    public function getIngredients(App_Model_User $user, $search)
    {
        if ($search == false || mb_strlen($search, 'UTF-8') == 0) {
            throw new Exception('Search invalid', 400);
        }
       return App_Model_Ingredient::fetchAll([
           'userId' => (string) $user->id,
           'title' => new MongoRegex("/$search/i")
       ], null, 10);
    }
    /**
     * @param App_Model_User $user
     * @param string $sectionId
     *
     * @return App_Model_Section|false
     * @throws Exception
     */
    public function getSection(App_Model_User $user, $sectionId)
    {
        $section = App_Model_Section::fetchOne([
            'userId' => (string)$user->id,
            'id' => $sectionId
        ]);

        if (!$section) {
            throw new Exception('section-not-found', 400);
        }

        return $section;
    }

    /**
     * @param App_Model_User $user
     * @param string $parentId
     *
     * @return App_Model_Section[]
     * @throws Exception
     */
    public function getSectionList(App_Model_User $user, $parentId = null)
    {
        $condition = [
            'userId' => (string) $user->id,
            'parentId' => $parentId
        ];

        if (!empty($parentId)) {
            $parentSection = App_Model_Section::fetchOne([
                'userId' => (string)$user->id,
                'id' => $parentId
            ]);

            if (!$parentSection) {
                throw new Exception('parent-section-not-found', 400);
            }
            $condition['parentId'] = $parentId;
        }

        return App_Model_Section::fetchAll($condition);
    }

    /**
     * @param App_Model_User $user
     * @param App_Model_Section $section
     * @param App_Model_Section $parentSection
     * @param string $title
     * @param string $imageBlob
     *
     * @return App_Model_Section
     * @throws Exception
     */
    public function saveSection(
        App_Model_User $user,
        App_Model_Section $section,
        App_Model_Section $parentSection = null,
        $title,
        $imageBlob
    ) {
        // Check if section belongs to user
        if (!empty($section->userId) && $section->userId != (string)$user->id) {
            throw new Exception('user-is-invalid', 400);
        }

        // Check if parent section belongs to user
        if (!empty($parentSection)) {
            if ($parentSection->userId != (string)$user->id) {
                throw new Exception('parent-id-is-invalid');
            }
            $section->parentId = (string)$parentSection->id;
        }

        $section->userId = (string)$user->id;
        $section->title = $title;

        // Checking title validity
        if (mb_strlen($section->title, 'UTF-8') < 3 || mb_strlen($section->title, 'UTF-8') > 30){
            throw new Exception('title-is-invalid', 400);
        }

        // Uploading new image and deleting old image
        if (!empty($imageBlob)) {
            $image = $this->loadImage($imageBlob);
            if (!empty($section->image)) {
                $this->deleteImageFromStorage($section->image['identity']);
            }
            $section->image = $image;
        }

        // Checking image validity
        if (!$section->image) {
            throw new Exception('image-is-invalid', 400);
        }

        $section->save();
        return $section;
    }

    /**
     * @param App_Model_User $user
     * @param App_Model_Section $section
     *
     * @throws Exception
     */
    public function deleteSection(App_Model_User $user, App_Model_Section $section)
    {
        if ((string) $user->id != $section->userId) {
            throw new Exception('Permission denied', 400);
        }
        $ids = $this->_getTreeOfSection($section);
        App_Model_Section::remove([
            'id' => ['$in' => $ids]
        ]);
        $products = App_Model_Product::fetchAll([
            'sectionId' => ['$in' => $ids]
        ]);
        foreach ($products as $item) {
            $item->sectionId = null;
            $item->save();
        }
    }

    /**
     * @param App_Model_Section $section
     * @param array $ids
     *
     * @return integer[]
     */
    private function _getTreeOfSection(App_Model_Section $section, array $ids = [])
    {
        $parentId = (string)$section->id;
        $ids [] = $parentId;

        $childSections = App_Model_Section::fetchAll([
            'parentId' => $parentId
        ]);

        foreach ($childSections as $child) {
            $ids = $this->_getTreeOfSection($child, $ids);
        }

        return $ids;
    }

    /**
     * @param App_Model_User $user
     * @param App_Model_Section $section
     * @param App_Model_Product $product
     * @param string $title
     * @param string $description
     * @param float $price
     * @param integer $weight
     * @param array $images
     * @param array $ingredietns
     * @param array $options
     * @param bool $exists
     *
     * @throws Exception
     * @return \App_Model_Product
     */
    public function saveProduct(
        App_Model_User $user,
        App_Model_Section $section,
        App_Model_Product $product = null,
        $title,
        $description,
        $price,
        $weight,
        $images,
        array $ingredietns = null,
        array $options = null,
        $exists
    )
    {
        if (!$product) {
            $product = new App_Model_Product();
            $product->userId = (string) $user->id;
        }
        elseif ($product->userId != (string) $user->id) {
            throw new Exception('permission-denied', 400);
        }
        if (!$title || mb_strlen($title, 'UTF-8') < 2 || mb_strlen($title, 'UTF-8') > 20) {
            throw new Exception('product-title-invalid', 400);
        }
        if (!$description || mb_strlen($description, 'UTF-8') < 2 || mb_strlen($description, 'UTF-8') > 300) {
            throw new Exception('product-description-invalid', 400);
        }
        if (gettype($price + 0.0) != 'double' || $price < 0) {
            throw new Exception('product-price-float', 400);
        }
        if (gettype($weight + 0) != 'integer' || $weight <= 0) {
            throw new Exception('product-weight-invalid', 400);
        }
        if ($section->userId  != (string) $user->id) {
            throw new Exception('section-invalid', 400);
        }
        if (!$product->id && !$images || count($images) == 0){
            throw new Exception('images-invalid', 400);
        }
        elseif ($images) {
            $oldImages = $product->images;
            $product->images = $this->loadImages($images);
            $this->deleteImagesFromStorage($oldImages);
        }
        if ($ingredietns && ! is_array($ingredietns)) {
            throw new Exception('ingredient-invalid');
        }
        elseif ($ingredietns) {
           foreach ($ingredietns as $item) {
               if (!App_Model_Ingredient::fetchOne(['id' => $item['id']])) {
                   throw new Exception('ingredient-not-found', 400);
               }
               if ($item['weight'] <= 0) {
                   throw new Exception('ingredient-weight-invalid', 400);
               }
               if ($item['price'] < 0) {
                  throw new Exception('ingredient-price-invalid', 400);
               }
           }
        }
        if ($options && ! is_array($options)) {
            throw new Exception('option-invalid');
        }
        elseif ($options) {
            foreach ($options as $item) {
                if (mb_strlen($item['title'],  'UTF-8') < 2 || mb_strlen($item['title'],  'UTF-8') > 20) {
                    throw new Exception('option-title-invalid', 400);
                }
                if ($item['weight'] <= 0) {
                    throw new Exception('option-weight-invalid', 400);
                }
                if ($item['price'] < 0) {
                    throw new Exception('option-price-invalid', 400);
                }
            }
        }
        $product->sectionId = (string) $section->id;
        $product->title = $title;
        $product->description = $description;
        $product->price = floatval($price);
        $product->weight = intval($weight);
        $product->sectionId = (string) $section->id;
        $product->exists = boolval($exists);
        $ingredietns && $product->ingredients = $ingredietns;
        $options && $product->options = $options;
        $product->save();
        return $product;
    }

    /**
     * @param App_Model_User $user
     * @param integer $offset
     * @param integer $limit
     * @param App_Model_Section $section
     * @param string $query
     *
     * @throws Exception
     * @return App_Model_Product[]
     */
    public function getProductList(App_Model_User $user, App_Model_Section $section = null, $offset, $limit, $query = null)
    {
        $conditions = ['userId' => (string) $user->id];
        if ($section && $section->userId != (string) $user->id) {
            throw new Exception('permission-denied', 400);
        }
        elseif ($section) {
            $conditions ['sectionId'] = (string) $section->id;
        }
        if ($query) {
            $conditions ['title'] = new MongoRegex("/$query/i");
            $conditions ['description'] = new MongoRegex("/$query/i");
        }

        return App_Model_Product::fetchAll($conditions, null, (int)$limit, (int)$offset);
    }

    /**
     * @param App_Model_User $user
     * @param App_Model_Section $section
     * @param string $query
     *
     * @throws Exception
     * @return int
     */
    public function getProductCount(App_Model_User $user, App_Model_Section $section = null, $query = null)
    {
        $conditions = ['userId' => (string) $user->id];
        if ($section && $section->userId != (string) $user->id) {
            throw new Exception('permission-denied', 400);
        }
        elseif ($section) {
            $conditions ['sectionId'] = (string) $section->id;
        }
        if ($query) {
            $conditions ['title'] = new MongoRegex("/$query/");
            $conditions ['description'] = new MongoRegex("/$query/");
        }

        return App_Model_Product::getCount($conditions);
    }

    /**
     * @param App_Model_User $user
     * @param string $productId
     *
     * @return App_Model_Product|null
     * @throws Exception
     */
    public function getProduct(App_Model_User $user, $productId)
    {
        $product = App_Model_Product::fetchOne(['id' => $productId]);
        if (!$product) {
            throw new Exception('not-found', 400);
        }
        if ( (string)$product->userId != (string)$user->id) {
            throw new Exception('permission-denied', 400);
        }
        return $product;
    }

    /**
     * @param App_Model_User $user
     * @param App_Model_Product $product
     *
     * @throws Exception
     */
    public function deleteProduct(App_Model_User $user, App_Model_Product $product)
    {
        if ($product->userId != (string) $user->id) {
           throw new Exception('permission-denied', 400);
        }
        $product->delete();
    }

    /**
     * @param App_Model_User $user
     *
     * @return array
     */
    public function treeSections(App_Model_User $user)
    {
        $models = App_Model_Section::fetchAll([
            'userId' => (string) $user->id
        ]);
        $modelsArray = [];
        $res = [];
        foreach ($models as $model) {
            $res [(string) $model->id] = [];
            $modelsArray [(string)$model->id] = $model;
        }
        foreach($models as $model) {
            $res[$model->parentId][] = $model;
        }
        $toPrint = [
            "sections" => []
        ];

        foreach($models as $model){
            if ( count($res[(string)$model->id]) == 0){
                if(empty($model->parentId)){
                    $toPrint ['sections'] [(string)$model->id] = [
                        'id' => (string) $model->id,
                        'title' => $model->title
                    ];
                    continue;
                }
                if(empty( $toPrint ['sections'] [$model->parentId]))
                    $toPrint ['sections'] [$model->parentId] = [
                        'id' => (string) $modelsArray [$model->parentId]->id,
                        'title' => $modelsArray [$model->parentId]->title,
                        'sub-sections' =>  [[
                            'id' => (string) $model->id,
                            'title' => $model->title
                        ]]
                    ];
                else {
                    $toPrint ['sections'] [$model->parentId] ['sub-sections'] [] = [
                        'id' => (string) $model->id,
                        'title' => $model->title
                    ];
                }
            }
        }

        $i = 0;
        foreach(array_keys($toPrint['sections']) as $item){
            $toPrint ['sections'] [$i++] = $toPrint['sections'] [$item];
            unset($toPrint['sections'] [$item]);
        }

        return $toPrint['sections'];
    }
} 