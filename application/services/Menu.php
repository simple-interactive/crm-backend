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
            throw new Exception('ingredient-title-invalid');
        }
        if (!$ingredient) {
            $ingredient = new App_Model_Ingredient();
            $ingredient->userId = (string) $user->id;
        }
        elseif ($ingredient->userId != (string) $user->id) {
            throw new Exception('permission-denied');
        }
        if (App_Model_Section::fetchOne([
            'userId' => (string) $user->id,
            'title' => $title
        ])) {
           throw new Exception('already-exists');
        }
        $ingredient->title = $title;
        $ingredient->save();
        return $ingredient;
    }

    /**
     * @var App_Model_User $user
     * @var App_Model_Product $product
     *
     * @return App_Model_Ingredient[]
     */
    public function getIngredients(App_Model_User $user)
    {
       return App_Model_Ingredient::fetchAll([
           'userId' => (string) $user->id
       ]);
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
            throw new Exception('Permission denied');
        }
        $ids = $this->_getTreeOfSection($section);
        App_Model_Section::remove([
            'id' => ['$in' => $ids]
        ]);
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
     * @param bool $exists
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
        $ingredietns,
        $exists
    )
    {
        if (!$product) {
            $product = new App_Model_Product();
            $product->userId = (string) $user->id;
        }
        elseif ($product->userId != (string) $user->id) {
            throw new Exception('permission-denied');
        }
        if (!$title || mb_strlen($title, 'UTF-8') < 2 || mb_strlen($title, 'UTF-8') > 20) {
            throw new Exception('product-title-invalid');
        }
        if (!$description || mb_strlen($description, 'UTF-8') < 2 || mb_strlen($description, 'UTF-8') > 300) {
            throw new Exception('product-description-invalid');
        }
        if (gettype($price + 0.0) != 'double' || $price < 0) {
            throw new Exception('product-price-float');
        }
        if (gettype($weight + 0) != 'integer' || $weight <= 0) {
            throw new Exception('product-weight-invalid');
        }

        if (!in_array($exists, ['true', 'false'])) {
            throw new Exception('product-exists-invalid');
        }
        if ($section->userId  != (string) $user->id) {
            throw new Exception('section-invalid');
        }
        if (!$product->id && !$images || count($images) == 0){
            throw new Exception('Images invalid');
        }
        elseif ($images) {
            $oldImages = $product->images;
            $product->images = $this->loadImages($images);
            $this->deleteImagesFromStorage($oldImages);
        }
        $product->sectionId = (string) $section->id;
        $product->title = $title;
        $product->description = $description;
        $product->price = floatval($price);
        $product->weight = intval($weight);
        $product->sectionId = (string) $section->id;
        $product->exists = $exists;
        $product->save();
        return $product;
    }

    /**
     * @param App_Model_User $user
     * @param integer $offset
     * @param integer $limit
     *
     * @return App_Model_Product[]
     */
    public function getProductList(App_Model_User $user, $offset, $limit)
    {
        return App_Model_Product::fetchAll([
            'userId' => (string) $user->id
        ], null, (int)$limit, (int)$offset);
    }

    public function getProductCount(App_Model_User $user)
    {
        return App_Model_Product::getCount([
            'userId' => (string) $user->id
        ]);
    }
} 