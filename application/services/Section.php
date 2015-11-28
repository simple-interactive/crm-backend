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
    public function add(App_Model_Menu $menu, App_Model_User $user, $title, $imageBlob, $parentId)
    {
        if(!$menu || $menu->userId != (string) $user->id){
            $this->setLastError('Menu invalid');
            return false;
        }
        if (!$title){
            $this->setLastError('Title empty');
            return false;
        }
        if($parentId !== false && ! App_Model_Section::fetchOne(['id' => $parentId])){
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
            'menuId' => (string)$menu->id,
            'title' => $title,
            'image' => $image,
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
        $menu = App_Model_Menu::fetchOne(['id' => $section->menuId]);
        if ( ! $menu && $menu->userId != (string)$user->id){
            $this->setLastError('Menu invalid');
            return false;
        }
        if (!empty($title)){
            $section->title = $title;
        }
        if ($parentId !== false && App_Model_Section::fetchOne(['id' => $parentId])){
            $section->parentId = $parentId;
        }
        $image = $this->imageService->loadImage($imageBlob);
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
    public function get(App_Model_User $user, App_Model_Menu $menu)
    {
        if ( (string)$user->id != (string)$menu->userId){
            $this->setLastError('Permission denied');
            return false;
        }
        return App_Model_Section::fetchAll([
            'menuId' => (string) $menu->id
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