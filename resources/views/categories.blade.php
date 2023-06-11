@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach($categories as $category)
            <div class="bg-dark px-3 py-2 text-white">{{$category->name}}</div>
            @foreach($category->subcategories as $subcategory)
                <div class="ms-5">{{$subcategory->name}}</div>
            @endforeach
        @endforeach
    </div>
@endsection
