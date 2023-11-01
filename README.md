### https://laravel.com/docs/10.x/installation
1. down source ```composer create-project laravel/laravel```
2. Cấu hình Database
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=xxxx
```
3. Hello world
routes/web.php
```Route::get('/hello-world', 'DevController@index')->name('dev.index');```

\app\Http\Controllers\DevController.php
```
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class DevController extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index(Request $request)
    {
        dd("vao dev");
    }
}
```


