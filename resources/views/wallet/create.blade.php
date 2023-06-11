@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="/wallets/store" method="post">
            @csrf
            <div class="row mb-3">
                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Wallet Name') }}</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                           value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="color" class="col-md-4 col-form-label text-md-end">{{ __('Color') }}</label>

                <div class="col-md-6">
                    <input id="color" type="color" class="form-control @error('color') is-invalid @enderror"
                           name="color"
                           value="{{ old('color') }}" required autocomplete="color" autofocus>
                    @error('color')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="initial-balance" class="col-md-4 col-form-label text-md-end">{{ __('Initial Balance') }}</label>

                <div class="col-md-6">
                    <input id="initial-balance" type="number"
                           class="form-control @error('initial-balance') is-invalid @enderror" name="initial-balance"
                           value="{{ old('initial-balance') }}" required autocomplete="initial-balance" autofocus>
                    @error('initial-balance')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="include-stats" class="col-md-4 col-form-label text-md-end">{{ __('Include to statistics') }}</label>

                <div class="col-md-6">
                    <!-- Rounded switch -->
                    <label class="switch">
                        <input name="include-stats" type="checkbox" value="1" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <label for="dont-include-stats" class="col-md-4 col-form-label text-md-end">{{ __('Don\'t Include to statistics') }}</label>
                <div class="col-md-6">
                    <!-- Rounded switch -->
                    <label class="switch">
                        <input name="dont-include-stats" type="checkbox" value="0">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save Wallet') }}
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection
