<?php

class App_Service_Menu
{
    /**
     * @var string
     */
    private $_lastError;

    /**
     * @param App_Model_User $user
     * @param string $title
     * @param integer $weight
     * @param float $price
     *
     * @return App_Model_Ingredient|false
     */
    public function createIngredient(App_Model_User $user, $title, $weight, $price)
    {
        if (!$title || strlen($title) < 2 && strlen($title) > 20){
            $this->setLastError('Title invalid');
            return false;
        }
        if($weight <= 0){
            $this->setLastError('Weight invalid');
            return false;
        }
        if($price  <= 0){
            $this->setLastError('Price invalid');
            return false;
        }
        $ingredient = new App_Map_Ingredient([
            'userId' => (string) $user->id,
            'title' => $title,
            'weight' => $weight,
            'price' => $price
        ]);
        return $price;
    }

    /**
     * @param App_Model_Ingredient $ingredient
     * @param string|false $title
     * @param integer|false $weight
     * @param float|false $price
     *
     * @return App_Model_Ingredient|false
     */
    public function editIngredient(App_Model_Ingredient $ingredient, $title = false, $weight = false, $price = false)
    {
        if (!$ingredient){
            $this->setLastError('Ingredient invalid');
            return false;
        }
        if ($title && strlen($title) < 2 && strlen($title) > 20){
            $this->setLastError('Title invalid');
            return false;
        }
        if($weight && $weight <= 0){
            $this->setLastError('Weight invalid');
            return false;
        }
        if($price && $price  <= 0){
            $this->setLastError('Price invalid');
            return false;
        }

        ! $title && $ingredient->title = $title;
        ! $weight && $ingredient->weight = $weight;
        ! $price && $ingredient->price  = $price;

        $ingredient->save();
        return $ingredient;
    }

    /**
     * @var App_Model_User $user
     * @var App_Model_Product $product
     *
     * @return App_Model_Ingredient[]|false
     */
    public function getIngredients(App_Model_User $user, App_Model_Product $product)
    {
       // if $prodect not found return false;
       return App_Model_Ingredient::fetchAll([
           'userId' => (string) $user->id
       ]);
    }
    /**
     * @param string $lastError
     */
    public function setLastError($lastError)
    {
        $this->_lastError = $lastError;
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->_lastError;
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
            $image = $this->_imageService->loadImage($imageBlob);
            if (!empty($section->image)) {
                $this->_imageService->deleteImageFromStorage($section->image['identity']);
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
     * @return array
     */
    public function deleteSection(App_Model_User $user, App_Model_Section $section)
    {
        if (!$section || (string) $user->id != $section->userId) {
            throw new Exception('Section not found');
        }
        $ids = $this->_getIdsForDelete($section);
        App_Model_Section::remove([
            'id' => ['$in' => $ids]
        ]);
    }

    private function _getIdsForDelete(App_Model_Section $section, array $ids = []){
        $parentId = (string)$section->id;
        $ids [] = $parentId;

        $childSections = App_Model_Section::fetchAll([
            'parentId' => $parentId
        ]);

        foreach($childSections as $child){
            $ids = $this->_getIdsForDelete($child, $ids);
        }

        return $ids;
    }
} 