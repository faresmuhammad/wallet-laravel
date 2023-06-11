@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="/wallets/create" class="btn btn-primary">Create Wallet</a>
        @foreach($walletsWitBalance as $walletAndBalance)
            {{--            @dd($walletAndBalance)--}}
            <a href="/records/{{$walletAndBalance['wallet']->id}}" class="btn">

                <div id="wallet" class="d-flex justify-content-lg-between align-items-center py-2 px-5 my-3"
                     style="border-radius:10px;background-color: {{$walletAndBalance['wallet']->color}};">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-white me-5">{{$walletAndBalance['wallet']->name}}</span>
                        <span class="text-white me-5">{{$walletAndBalance['balance']->value}}</span>
                    </div>
                    <div class="control">
                        <a href="{{route('newrecord',['wallet'=>$walletAndBalance['wallet']->id])}}"
                           class="btn btn-secondary">New Record</a>
                        <a href="/wallets/{{$walletAndBalance['wallet']->id}}/edit" class="btn btn-warning">Edit</a>
                        <form action="/wallets/{{$walletAndBalance['wallet']->id}}" style="display: inline;"
                              method="post">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endsection
