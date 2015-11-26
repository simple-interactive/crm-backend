<?php

class App_Service_Menu {

    /**
     * @param array $menuParams
     * @return App_Model_Menu
     */
    public function create(array $menuParams)
    {
        if ( empty($menuParams['name']))
            throw new InvalidArgumentException('Name is empty', 400);

        $image = $this->loadImage();

        $menu = new App_Model_Menu(array_merge($menuParams, ['image' => $image]));

        if ( ! $menu->save()){
            throw new RuntimeException('Save failed');
        }

        return $menu;
    }

    protected function loadImage(){

        $upload = new Zend_File_Transfer();

        if ( !$upload->getFileInfo() && empty($upload->getFileInfo()['image'])) {
            throw new InvalidArgumentException('Image is empty');
        }

        $image = $upload->getFileInfo() ['image'];
        $image = $this->renameFile($image);

        $config = Zend_Registry::get('config');
        $storage = new \Storage\Storage();
        $storage->setToken($config['storage']['token']);

        $files =  $storage->upload($image['name'], \Storage\Storage::FILE);
        unlink($image['name']);

        return [
            'url' => $files[0]->getUrl(),
            'identity' => $files[0]->getIdentity()
        ];
    }

    protected function renameFile(array $image){
        $newName = dirname($image['tmp_name']). DIRECTORY_SEPARATOR . $image['name'];

        if ( ! rename($image['tmp_name'], $newName)){
            throw new \RuntimeException('Can not rename image');
        }

        $image ['name'] = $newName;
        return $image;
    }

    public function getAll($userId){
        return App_Model_Menu::fetchAll();
    }

    public function delete($id, $userId){
        $menu = $this->get($id);
        return $menu->delete();
    }

    public function edit($id, $data){

        $menu = $this->get($id);

        if ( (string)$menu->userId != $data['userId']){
            throw new Exception('Permission denied', 403);
        }

        if ($data ['name'] !== false && ! empty($data['name']))
            $menu->name = $data ['name'];

        try{
            $image = $this->loadImage();
            $this->deleteImageFromStorage($menu->image['identity']);
            $menu->image = $image;
        }catch (Exception $e){
            if ($e->getMessage() != 'Image is empty') {
                throw $e;
            }
        }

        if ( ! $menu->save()){
            throw new RuntimeException('Save failed!');
        }

        return $menu;
    }


    protected function get($id){

        if ($id === false && empty($id)){
            throw new InvalidArgumentException('Id invalid', 400);
        }

        $menu = App_Model_Menu::fetchOne([
            'id' => $id
        ]);

        if (! $menu){
            throw new Exception('Menu not found', 404);
        }

        return $menu;
    }

    protected function deleteImageFromStorage($identity){
        $config = Zend_Registry::get('config');
        $storage = new \Storage\Storage();
        $storage->setToken($config['storage']['token']);

        return $storage->delete($identity);
    }
}