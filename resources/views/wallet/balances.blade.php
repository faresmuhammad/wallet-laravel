@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach($balances as $balance)
            <div id="balance" class="d-flex justify-content-lg-between bg-dark py-2 px-5 my-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-white me-5">{{$balance->value}}</span>
                    <span class="text-white me-5">{{$balance->currency->name}}</span>
                </div>
                <div class="control">
                    <a href="/wallets/{{$balance->wallet->id}}/balances/{{$balance->id}}/edit" class="btn btn-warning">Edit</a>

                </div>
            </div>
        @endforeach
        </div>
@endsection
