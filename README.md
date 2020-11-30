# laravel-helper
> Helper file for my laravel projects
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

```
