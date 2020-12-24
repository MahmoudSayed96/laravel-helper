# laravel-helper
> Helper file for my laravel projects
> This file contains some functions that you using in each project you create.
***
```php
<?php
/**
* @file
* Contains App/Helpers/Helper.php.
*
* Implements php helper functions using in my project.
*
*/

use Illuminate\Support\Str;

define('DS', DIRECTORY_SEPARATOR);

/**
  * @param $routeName ex.departments 
  *  @return bool
  */
if(!function_exists('is_current_route')) {
     function is_current_route($route_name = '') {
      return NULL !== request()->segment(2) && request()->segment(2) == $route_name ? true : false;
     }
}

/**
 * Implements function to get current user.
 *
 * @return App\User $user.
 *  Return current user object.
 */
if (!function_exists('current_user')) {
    function current_user() {
        return auth()->user();
    }
}

/**
 * Create slug.
 *
 * @param string $src.
 *  Sources string.
 * @return string $slug
 * Return slug.
 */
if (!function_exists('slug')) {
    function slug(string $value = NULL)
    {
        return strtolower(trim(str_replace(' ', '-', $value)));
    }
}

/**
 * Get admin based on guard.
 * Return guard admin.
 */
if (!function_exists('admin')) {
    function admin()
    {
        return auth()->guard('admin');
    }
}

 /**
   * Get dashboard route name.
   * Change dashboard/admin prefix name from one place.
   * 
   * @param $route.
   * Route name 'sections.index'
   * @param $data.
   * 
   * @return String
   * Return name of route.
   */
  if (!function_exists('admin_route_name')) {
    function admin_route_name($route = 'welcome', $data=[])
    {
        return 'admin.' . $route;
    }
  }
  
  /**
   * Get dashboard route name.
   * Change dashboard/admin prefix name from one place.
   * 
   * @param $route.
   * Route name 'sections.index'
   * @param $data.
   * 
   * @return \Illuminate\Http\Response $route_object.
   */
  if (!function_exists('admin_route')) {
    function admin_route_name($route = 'welcome', $data=[])
    {
        return route('admin.' . $route, $data);
    }
}

```
> php trait file for handling upload imags based on this package `http://image.intervention.io/getting_started/installation`

```php
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
```
> PHP trait for handling exception error messages.

```php
<?php

namespace App\Traits;

/**
 * Custom redirect messages.
 */
trait RedirectMessagesTrait {

    /**
     * Implement function for redirect if item not exists.
     *
     * @param string $route
     *  Route name.
     * @param mixed $data
     *  Extra data.
     * @return \Illuminate\Http\Response
     *  Return response.
     */
    public function redirectIfNotFound($route, $message=null, $data = null) {
        return redirect()->route($route,$data)->with([
            'message' => $message !== null ? $message : 'هذا العنصر غير موجود',
            'messageType' => 'error'
        ]);
    }

    /**
     * Implement function for redirect if success.
     *
     * @param string $route
     *  Route name.
     * @param string $message
     *  Success message.
     * @param mixed $data
     *  Extra data.
     * @return \Illuminate\Http\Response
     *  Return response.
     */
    public function redirectIfSuccess($route = null,$message = null, $data = null) {
        return redirect()->route($route,$data)->with([
            'message' => $message,
            'messageType' => 'success'
        ]);
    }

    /**
     * Implement function for redirect if error.
     *
     * @param string $route
     *  Route name.
     * @param string $message
     *  Error message.
     * @param mixed $data
     *  Extra data.
     * @return \Illuminate\Http\Response
     *  Return response.
     */
    public function redirectIfError($route = null,$message = null, $data = null) {
        return redirect()->route($route,$data)->with([
            'message' => $message !== null ? $message : 'حدث خطأ ما يرجى المحاولة لاحقا ',
            'messageType' => 'error'
        ]);
    }
}

```
