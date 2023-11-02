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
```
	@include('v2.partials.navbar')
	@yield('content')
	@include('v2.partials.footer')
```
File extends \resources\views\v2\dev\show.blade.php
	```@extends('v2.master')```
	
###  CRUD

## Setup cơ bản
composer require "laravelcollective/html"

config/app.php
```
'providers' => [
    Collective\Html\HtmlServiceProvider::class,
],
'aliases' => [
   'Form' => Collective\Html\FormFacade::class,
   'Html' => Collective\Html\HtmlFacade::class,
],
```

## Setup Database
\.env
```
# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Clear cache config
php artisan config:clear

## Tạo table products
```
create table `products` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar (750),
	`price` int (11),
	`content` text ,
	`active` int (1),
	`createtime` timestamp 
);
```

## Tạo một controller tên là ProductController
php artisan make:controller ProductController --resource

artisan sẽ tạo ra phương thức cơ bản:
```
1)index()
2)create()
3)store()
4)show()
5)edit()
6)update()
7)destroy()
```

## Thiết lập Route
routes/web.php
```
Route::prefix('product')->group(function () {
	Route::get('/', 'App\Http\Controllers\ProductController@index');
	
	Route::get('/create', 'App\Http\Controllers\ProductController@create');
	Route::post('/', 'App\Http\Controllers\ProductController@store');
	
	Route::get('/{product_id}/edit', 'App\Http\Controllers\ProductController@edit');	
	Route::put('/{product_id}', 'App\Http\Controllers\ProductController@update');
	
	Route::get('/{product_id}/delete', 'App\Http\Controllers\ProductController@destroy');	
});
```


## Bắt đầu code phần index
\app\Http\Controllers\ProductController.php
```
    public function index(){
		$products = DB::table('products')->get();
		return view('frontend.product.list')->with('products', $products);
    }
```

Master Blade Files: \resources\views\layouts\default.blade.php, navbar.blade.php, sidebar.blade.php
```
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>dev | @yield('title')</title>

  </head>
  <body>
    @include('layouts.navbar')
    <div class="container-fluid">
        <div class="row">
            @include('layouts.sidebar')
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                @yield('content')                
            </main>
        </div>
    </div>
  </body>
</html>
```

Blade Files: \resources\views\frontend\product\list.blade.php
```
@extends('layouts.default')

@section('title', 'Danh sách sản phẩm')

@section('content')
    <table class="table table-bordered">
        <tr class="success">
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Giá sản phẩm</th>
            <th>Nội dung</th>
            <th>Đăng bán</th>
            <th>Action</th>
        </tr>
        @foreach($products as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->name }}</td>
            <td class="text-right">{{ number_format($p->price) }}</td>
            <td>{{ $p->content }}</td>  
            <td>
                @if($p->active)
                    <span class="text-success glyphicon glyphicon-ok"></span>
                @else
                    <span class="text-danger glyphicon glyphicon-remove"></span>
                @endif
            </td>
            <td>
                  <a href="{{ route('product.edit', ['product_id' =>  $p->id]) }}"><span class="glyphicon glyphicon-pencil">Edit</span></a>
                <a href="{{ route('product.delete', ['product_id' =>  $p->id]) }}"><span class="glyphicon glyphicon-trash">Delete</span></a>
            </td>
        </tr>
        @endforeach
    </table>
@endsection
```

## Bắt đầu code phần create
\app\Http\Controllers\ProductController.php
```
    public function create(){
	   return view('frontend.product.create');				
    }
	
	public function store(Request $request){
		$validator = Validator::make($request->all(), [
			'name'       => 'required|max:255',
			'price'      => 'required|numeric',
			'content'    => 'required'
		]);

		if ($validator->fails()) {
			return redirect('product/create')
					->withErrors($validator)
					->withInput();
		} else {
			$active = $request->has('active')? 1 : 0;
			$product_id = DB::table('products')->insertGetId(
			[
				'name'       => $request->input('name'),
				'price'      => $request->input('price'),
				'content'    => $request->input('content'),
				'active'     => $active
			]
				);
			return redirect('product/create')
					->with('success', 'Sản phẩm được tạo thành công với ID: ' . $product_id);
		}
	}
```

Blade Files: \resources\views\frontend\product\create.blade.php
```
@extends('layouts.default')

@section('title', 'Tạo sản phẩm')

@section('content')
    @if(isset($success))
    <div class="alert alert-success" role="alert">{{ $success }}</div>
    @endif
    @if(isset($fail))
    <div class="alert alert-danger" role="alert">{{ $fail }}</div>
    @endif

    {!! Form::open(array('url' => '/product', 'class' => 'form-horizontal')) !!}
      <div class="form-group">
         {!! Form::label('name', 'Tên sản phẩm', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-9">
            {!! Form::text('name', '', array('class' => 'form-control')) !!}
         </div>
      </div>

      <div class="form-group">
         {!! Form::label('price', 'Giá sản phẩm', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-3">
            {!! Form::text('price', '', array('class' => 'form-control')) !!}
         </div>
      </div>

      <div class="form-group">
         {!! Form::label('content', 'Nội dung sản phẩm', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-9">
            {!! Form::textarea('content', '', array('class' => 'form-control', 'rows' => 3)) !!}
         </div>
      </div>

      <div class="form-group">
         {!! Form::label('image_path', 'Ảnh sản phẩm', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-9">
            {!! Form::text('content', '', array('class' => 'form-control')) !!}
         </div>
      </div>

      <div class="form-group">
         {!! Form::label('active', 'Active', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-3">
            {!! Form::checkbox('active', '', true) !!}
         </div>
      </div>  

      <div class="form-group">
         <div class="col-sm-offset-2 col-sm-10">
            {!! Form::submit('Tạo sản phẩm', array('class' => 'btn btn-success')) !!}
         </div>
      </div>
   {!! Form::close() !!}
@endsection
```

## Bắt đầu code phần edit
\app\Http\Controllers\ProductController.php
```
    public function edit($id){
		$product = DB::table('products')->find($id);
		return view('frontend.product.edit')->with(compact('product'));
    }
	
	public function update(Request $request, $id){
		$active = $request->has('active')? 1 : 0;
		$updated = DB::table('products')
			->where('id', '=', $id)
			->update([
				'name'       => $request->input('name'),
				'price'      => $request->input('price'),
				'content'    => $request->input('content'),
				'active'     => $active,
				'createtime' => \Carbon\Carbon::now()
				]);
		return redirect('product');
    }
```

Blade Files: \resources\views\frontend\product\edit.blade.php
```
@extends('layouts.default')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
    @if(isset($success))
    <div class="alert alert-success" role="alert">{{ $success }}</div>
    @endif
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::open(array('url' => '/product/' . $product->id, 'class' => 'form-horizontal', 'method' => 'put')) !!}
      <div class="form-group">
         {!! Form::label('name', 'Tên sản phẩm', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-9">
            {!! Form::text('name', $product->name, array('class' => 'form-control')) !!}
         </div>
      </div>

      <div class="form-group">
         {!! Form::label('price', 'Giá sản phẩm', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-3">
            {!! Form::text('price', $product->price, array('class' => 'form-control')) !!}
         </div>
      </div>

      <div class="form-group">
         {!! Form::label('content', 'Nội dung sản phẩm', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-9">
            {!! Form::textarea('content', $product->content, array('class' => 'form-control', 'rows' => 3)) !!}
         </div>
      </div>

      <div class="form-group">
         {!! Form::label('active', 'Active', array('class' => 'col-sm-3 control-label')) !!}
         <div class="col-sm-3">
            {!! Form::checkbox('active', $product->active, true) !!}
         </div>
      </div>  

      <div class="form-group">
         <div class="col-sm-offset-2 col-sm-10">
            {!! Form::submit('Chỉnh sửa sản phẩm', array('class' => 'btn btn-success')) !!}
         </div>
      </div>
   {!! Form::close() !!}
@endsection
```

## Bắt đầu code phần delete
\app\Http\Controllers\ProductController.php
```
    public function destroy($id){
        DB::table('products')
              ->where('id', '=', $id)         
              ->delete();
		return redirect('product');	  
    }
```

### API ( token)
```
	Client --> post login success --> 
	Tạo ra 1 token:
		+ update vào database
		+ response.token cho Client
		
	Client save token & sử dụng cho các lần call api sau
```

### Authentication -  Permission

Khi user login vào thành công thì sẽ có middleware checkUsersLogin (select table permission join user_permission where route_name) 

## Tạo Các table:
```
user
permission
user_permission
```

## Tạo Route
## Tạo Template
## Tạo UsersController
## Tạo Seeding user
## Tạo middleware
```php artisan make:middleware checkUsersLogin```

###  database migrations
```php artisan migrate log_users_activity```


### Muti language
```
/lang
    /en
        messages.php
		
	/vi
		messages.php
```

1. Tạo 1 Middleware localization để setLocale language lấy từ session
2. routes\web.php bọc middleware localization
```Route::group(['middleware' => 'localization'], function () { ... });```
3. Tạo 1 router để change language
``` Route::get('change-language/{language}', 'LanguageController@changeLanguage')->name('change_language'); ```
4. Chỗ show
```
{{ __('messages.welcome')}}
<br/>
{{ __('messages.welcome_you',['yourname'=> 'Mr.Hoan'])}}
```

### Collection từ lang


###  Session
###  Cached
###  Redis

###  Transaction
###  Middleware
###  Cronjob Scheduler
###  Listeners/Events

### Design pattern

### Other
##  Overrides class
##  Check data logs
##  Điều hướng router, view
##  Dùng muti DB

