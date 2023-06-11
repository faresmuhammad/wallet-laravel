@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Balance for {{$balance->wallet->name}}</h1>
        <form action="/wallets/{{$balance->wallet->id}}/balances/{{$balance->id}}" method="post">
            @csrf
            @method('put')
            <div class="row mb-3">
                <label for="value" class="col-md-4 col-form-label text-md-end">{{ __('Value') }}</label>

                <div class="col-md-6">
                    <input id="value" type="number" class="form-control @error('value') is-invalid @enderror" name="value"
                           value="{{ old('value') ?? $balance->value}}" required autocomplete="value" autofocus>
                    @error('value')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save Balance') }}
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection
