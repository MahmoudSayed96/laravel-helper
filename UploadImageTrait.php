<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;

/**
 * Handle uploaded images.
 */
trait UploadImageTrait
{

    /**
     * Implement function for upload image file.
     *
     * @param string $folder Folder name for save image inside it.
     * @param $image  Upload file object.
     * @return  Return image file path after save.
     */
    public function uploadImage(string $folder, $image)
    {
        // Create folder if not exists.
         $path = public_path('/uploads/images/' . $folder);
        if(!File::exists($path)){
         File::makeDirectory($path);   
        }
        
        $imageName = $image->hashName();
        $path = 'uploads/images/' . $folder . '/' . $imageName;
        $img = Image::make($image);
        // Resize the image to a width of 300 and constrain aspect ratio (auto height).
        $img->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        // 50 for compress image to half.
        $img->save(public_path($path), 50);
        return $path;
    }

    /**
     * Implement function for delete image from folder.
     *
     * @param string $folder
     *  Folder name that contains images folders inside it.
     * @param $image
     *  Image name.
     */
    public function removeImage($image)
    {
        $imagePath = public_path($image);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    /**
     * Implement function for upload multiple images from folder.
     *
     * @param string $folder Folder name that contains images folders inside it.
     * @param $images Images from request.
     */
    public function uploadMultipleImages($folder, $images)
    {
        // Create folder if not exists.
         $path = public_path('/uploads/images/' . $folder);
        if(!File::exists($path)){
         File::makeDirectory($path);   
        }
        
        $images_arr = array();
        if ($files = $images) {
            foreach ($files as $file) {
                $image_path = $this->uploadImage($folder, $file);
                $images_arr[] = $image_path;
            }
        }
        // Implode images with pipe symbol
        $allImages = implode("|", $images_arr);
        return $allImages;
    }

    /**
     * Implement function for remove multiple images from folder.
     *
     * @param string $distention Folder name that contains images folders inside it.
     * @param $images Images from request.
     */
    public function removeMultipleImages($images)
    {
        foreach ($images as $image) {
            $this->removeImage($image);
        }
    }
    
     /**
     * Save images in specific directory in public path.
     * 
     * @param Illuminate\Http\Request $request 
     * @param String $inputName
     *  Name of file input in form.
     * @param Object $row.
     *  Object after updated in database.
     * @param String $folderName.
     *  Name of folder that will store images in it.
     * @param String $defaultImagePath.
     *  Path of default image used.
     */
    public function saveImage($request, $inputName, $row, $folderName, $defaultImagePath) {
        // Update site logo.
        if($request->has($inputName)) {
            $oldAvatar = $row->getAvatar();
            if(isset($oldAvatar) && $oldAvatar != $defaultImagePath) {
                // Remove old image.
                $this->removeImage($oldAvatar);
            }
            // Update with new image.
            $newAvatar = $this->uploadImage($folderName, $request->avatar);
            $row->update([
                $inputName => $newAvatar
            ]);
        }
    }

    private function DS()
    {
        return DIRECTORY_SEPARATOR;
    }
    
}
