<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
		$products = DB::table('products')->get();
		return view('frontend.product.list')->with('products', $products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
		$product = DB::table('products')->find($id);
		return view('frontend.product.edit')->with(compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        DB::table('products')
              ->where('id', '=', $id)         
              ->delete();
		return redirect('product');	  
    }
}
