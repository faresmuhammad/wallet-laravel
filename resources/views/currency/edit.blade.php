@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="/currencies/{{$currency->id}}" method="post">
            @csrf
            @method('put')
            <div class="row mb-3">
                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Currency Name') }}</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                           value="{{ old('name') ?? $currency->name}}" required autocomplete="name" autofocus>
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="pfx" class="col-md-4 col-form-label text-md-end">{{ __('PFX Symbol') }}</label>

                <div class="col-md-6">
                    <input id="pfx" type="text" class="form-control @error('pfx') is-invalid @enderror"
                           name="pfx"
                           value="{{ old('pfx') ?? $currency->pfx_symbol}}"  autocomplete="pfx" autofocus>
                    @error('pfx')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="sfx"
                       class="col-md-4 col-form-label text-md-end">{{ __('SFX Symbol') }}</label>

                <div class="col-md-6">
                    <input id="sfx" type="text"
                           class="form-control @error('sfx') is-invalid @enderror" name="sfx"
                           value="{{ old('sfx') ?? $currency->sfx_symbol}}"
                           autocomplete="sfx" autofocus>
                    @error('sfx')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="unit"
                       class="col-md-4 col-form-label text-md-end">{{ __('Unit Name') }}</label>

                <div class="col-md-6">
                    <input id="unit" type="text"
                           class="form-control @error('unit') is-invalid @enderror" name="unit"
                           value="{{ old('unit') ?? $currency->unit_name}}" required
                           autocomplete="unit" autofocus>
                    @error('unit')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="cent"
                       class="col-md-4 col-form-label text-md-end">{{ __('Cent Name') }}</label>

                <div class="col-md-6">
                    <input id="cent" type="text"
                           class="form-control @error('cent') is-invalid @enderror" name="cent"
                           value="{{ old('cent') ?? $currency->cent_name}}"
                           autocomplete="cent" autofocus>
                    @error('cent')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="scale"
                       class="col-md-4 col-form-label text-md-end">{{ __('Scale') }}</label>

                <div class="col-md-6">
                    <input id="scale" type="number"
                           class="form-control @error('scale') is-invalid @enderror" name="scale"
                           value="{{ old('scale') ?? $currency->scale}}" required
                           autocomplete="scale" autofocus>
                    @error('scale')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="symbol_name"
                       class="col-md-4 col-form-label text-md-end">{{ __('Symbol Name') }}</label>

                <div class="col-md-6">
                    <input id="symbol_name" type="text"
                           class="form-control @error('symbol_name') is-invalid @enderror" name="symbol_name"
                           value="{{ old('symbol_name') ?? $currency->symbol_name}}" required
                           autocomplete="symbol_name" autofocus>
                    @error('symbol_name')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save Currency') }}
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection
