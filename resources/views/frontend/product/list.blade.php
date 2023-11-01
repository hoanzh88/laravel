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