@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="text-danger">
            @if(\Illuminate\Support\Facades\Session::has('message'))
                {{session('message')}}
            @endif
        </div>
        <label for="type">Record Type</label><select id="type">
            <option value="Expense" selected>Expense</option>
            <option value="Income">Income</option>
            <option value="Transfer">Transfer</option>
        </select>
        <form action="{{route('pay',['wallet' => $wallet->id])}}" method="post" id="Expense" style="">
            @csrf
            <h3 id="record-type"></h3>
            <div class="row mb-3">
                <label for="amount" class="col-md-4 col-form-label text-md-end">{{ __('Amount') }}</label>

                <div class="col-md-6">
                    <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror"
                           name="amount" value="{{ old('amount') }}" required autocomplete="amount">

                    @error('amount')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="description" class="col-md-4 col-form-label text-md-end">{{ __('Description') }}</label>

                <div class="col-md-6">
                    <textarea id="description" class="form-control @error('description') is-invalid @enderror"
                              name="description">
                        {{ old('description') }}
                    </textarea>

                    @error('description')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="balance" class="col-md-4 col-form-label text-md-end">{{ __('Currency') }}</label>
                <div class="col-md-6">
                    <select name="balance" id="balance" class="form-control">
                        @foreach($balances as $balance)
                            <option value="{{$balance->id}}">{{$balance->currency->symbol_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="category" class="col-md-4 col-form-label text-md-end">{{ __('Category') }}</label>
                <div class="col-md-6">
                    <select name="category" id="category" class="form-control">
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                            {{--                            <optgroup label="{{$category->name}}">--}}
                            @foreach($category->subcategories as $subcategory)
                                <option value="{{$subcategory->id}}">&nbsp;&nbsp;{{$subcategory->name}}</option>
                            @endforeach
                            {{--                            </optgroup>--}}
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Pay') }}
                    </button>
                </div>
            </div>
        </form>
        <form action="{{route('topup',['wallet' => $wallet->id])}}" method="post" id="Income" style="display: none">
            @csrf
            <h3 id="record-type"></h3>
            <div class="row mb-3">
                <label for="amount" class="col-md-4 col-form-label text-md-end">{{ __('Amount') }}</label>

                <div class="col-md-6">
                    <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror"
                           name="amount" value="{{ old('amount') }}" required autocomplete="amount">

                    @error('amount')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="description" class="col-md-4 col-form-label text-md-end">{{ __('Description') }}</label>

                <div class="col-md-6">
                    <textarea id="description" class="form-control @error('description') is-invalid @enderror"
                              name="description">
                        {{ old('description') }}
                    </textarea>

                    @error('description')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="balance" class="col-md-4 col-form-label text-md-end">{{ __('Currency') }}</label>
                <div class="col-md-6">
                    <select name="balance" id="balance" class="form-control">
                        @foreach($balances as $balance)
                            <option value="{{$balance->id}}">{{$balance->currency->symbol_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="category" class="col-md-4 col-form-label text-md-end">{{ __('Category') }}</label>
                <div class="col-md-6">
                    <select name="category" id="category" class="form-control">
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                            {{--                            <optgroup label="{{$category->name}}">--}}
                            @foreach($category->subcategories as $subcategory)
                                <option value="{{$subcategory->id}}">&nbsp;&nbsp;{{$subcategory->name}}</option>
                            @endforeach
                            {{--                            </optgroup>--}}
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Top Up') }}
                    </button>
                </div>
            </div>
        </form>
        <form action="{{route('transfer',['wallet'=>$wallet->id])}}" method="post" id="Transfer" style="display: none">
            @csrf
            <div class="row mb-3">
                <label for="sender" class="col-md-4 col-form-label text-md-end">{{ __('Sender') }}</label>
                <div class="col-md-6">
                    <select name="sender" id="sender" class="form-control">
                        @foreach($wallets as $w)
                            <option value="{{$w->id}}" @if($w->id == $wallet->id) selected @endif>{{$w->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="receiver" class="col-md-4 col-form-label text-md-end">{{ __('Receiver') }}</label>
                <div class="col-md-6">
                    <select name="receiver" id="receiver" class="form-control">
                        @foreach($wallets as $w)
                            <option value="{{$w->id}}">{{$w->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="amount" class="col-md-4 col-form-label text-md-end">{{ __('Amount') }}</label>

                <div class="col-md-6">
                    <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror"
                           name="amount" value="{{ old('amount') }}" required autocomplete="amount">

                    @error('amount')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="currency" class="col-md-4 col-form-label text-md-end">{{ __('Currency') }}</label>
                <div class="col-md-6">
                    <select name="currency" id="currency" class="form-control">
                        @foreach($currencies as $currency)
                            <option value="{{$currency->id}}">{{$currency->symbol_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Transfer Money') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script type="module">
        $('#type').on('change', function () {
            $('#' + $(this).val()).show().siblings('form').hide();
            $('#form-type').val($(this).val());
        })
    </script>
@endsection
