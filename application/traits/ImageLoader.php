<?php

/**
 * @trait App_Trait_ImageLoader
 */
trait App_Trait_ImageLoader {

    /**
     * @param array $imageBlobs
     *
     * @return array
     */
    public function loadImages(array $imageBlobs)
    {
        $data = [];
        foreach ($imageBlobs as $image) {
            $data [] = $this->loadImage($image);
        }
        return $data;
    }

    /**
     * @param string $imageBlob
     *
     * @return array
     * @throws Exception
     */
    public function loadImage($imageBlob)
    {
        $data = explode(',', $imageBlob);
        if (count($data) != 2) {
            throw new Exception('image-is-invalid', 400);
        }
        $image = base64_decode($data[1]);
        $storage = new \Storage\Storage();
        $files =  $storage->upload([
            'content' => $image,
            'name' => md5(microtime()).'.png'
        ], \Storage\Storage::DATA);
        return $files[0]->asArray();
    }

    /**
     * @param string $identity
     *
     * @return bool
     */
    public function deleteImageFromStorage($identity)
    {
        $storage = new \Storage\Storage();
        return $storage->delete($identity);
    }
} 