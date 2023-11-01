Tham khảo: https://laravel.com/docs/10.x/installation
### Install & config cơ bản
## 1. down source ```composer create-project laravel/laravel```
## 2. Cấu hình Database
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=xxxx
```
## 3. Hello world
routes/web.php
```Route::get('/hello-world', 'DevController@index')->name('dev.index');```

check \app\Providers\RouteServiceProvider.php đã setup $namespace = 'App\Http\Controllers';

\app\Http\Controllers\DevController.php
```
<?php
    public function index(Request $request){
        dd("vao dev");
    }
```


## 4. Template jinja
Master Blade Files: \resources\views\v2\master.blade.php
	@include('v2.partials.navbar')
	@yield('content')
	@include('v2.partials.footer')
	
File extends \resources\views\v2\dev\show.blade.php
	@extends('v2.master')
	
