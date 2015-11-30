<?php

class App_Service_Section
{
    /**
     * @var string
     */
    private $_lastError;
    /**
     * @var App_Service_Image
     */
    private $_imageService;

    public function __construct()
    {
        $this->_imageService = new App_Service_Image();
    }

    /**
     * @param App_Model_Menu $sectionId
     * @param App_Model_User $user
     * @param string $title
     * @param string $imageBlob
     * @param string $parentId
     *
     * @return App_Model_Section|bool
     */
    public function create(App_Model_User $user, $title, $imageBlob, $parentId)
    {
        if (!$title){
            $this->setLastError('Title empty');
            return false;
        }
        if($parentId !== null && ! App_Model_Section::fetchOne(['id' => $parentId])){
            $this->setLastError('Parent Id invalid!');
            return false;
        }
        $image = $this->_imageService->loadImage($imageBlob);
        if (!$image)
        {
            $this->setLastError('Image invalid');
            return false;
        }
        $section = new App_Model_Section([
            'title' => $title,
            'image' => $image,
            'userId' => (string) $user->id,
            'parentId' => $parentId
        ]);
        if($parentId){
            $section->parentId = $parentId;
        }
        $section->save();
        return $section;
    }

    /**
     * @param string $sectionId
     * @param App_Model_User $user
     * @param string $title
     * @param string $imageBlob
     * @param string $parentId
     *
     * @return App_Model_Section|bool
     */
    public function edit($sectionId, App_Model_User $user, $title, $imageBlob, $parentId)
    {
        $section = App_Model_Section::fetchOne(['id' => $sectionId]);
        if ( ! $section){
            $this->setLastError('Section not found');
            return false;
        }
        if (!empty($title)){
            $section->title = $title;
        }
        if ($parentId !== false && App_Model_Section::fetchOne(['id' => $parentId])){
            $section->parentId = $parentId;
        }
        $image = $this->_imageService->loadImage($imageBlob);
        if ($image)
        {
            $this->_imageService->deleteImageFromStorage($section->image['identity']);
            $section->image = $image;
        }
        $section->save();
        return $section;
    }

    /**
     * @param App_Model_User $user
     * @param App_Model_Menu $menu
     */
    public function get(App_Model_User $user, $parentId)
    {
        return App_Model_Section::fetchAll([
            'userId' => (string) $user->id,
            'parentId' => $parentId
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

} 