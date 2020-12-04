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

if(!function_exists('is_current_route')) {
  /**
   * @param $route 
   *  @return bool
   */
   function is_current_route($route) {
      if(request()->route()->getName == $route)
        return true;

       return false;
   }
}

/**
 * Implements function to get current user.
 *
 * @return App\User $user.
 *  Return current user object.
 */
if (!function_exists('currentUser')) {
    function currentUser() {
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
```
