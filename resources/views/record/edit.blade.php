@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="text-danger">
            @if (\Illuminate\Support\Facades\Session::has('message'))
                {{ session('message') }}
            @endif
        </div>
        @if ($record->type == 'Expense' || $record->type == 'Income')
            <form action="{{ route('updateRecord', ['wallet' => $wallet->id, 'record' => $record]) }}" method="post"
                id="Expense" style="">
                <label for="type">Record Type</label>
                <select name="type" id="type">
                    <option value="Expense" @if ($record->type == 'Expense') selected @endif>Expense</option>
                    <option value="Income" @if ($record->type == 'Income') selected @endif>Income</option>
                    <option value="Transfer" @if ($record->type == 'Transfer') selected @endif>Transfer</option>
                </select>
                @csrf
                @method('put')
                <h3 id="record-type"></h3>
                <div class="row mb-3">
                    <label for="amount" class="col-md-4 col-form-label text-md-end">{{ __('Amount') }}</label>

                    <div class="col-md-6">
                        <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror"
                            name="amount" value="{{ old('amount') ?? $record->amount }}" required autocomplete="amount">

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
                        <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">
                    {{ old('description') ?? $record->description }}
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
                            @foreach ($balances as $balance)
                                <option value="{{ $balance->id }}" @if ($record->balance_id == $balance->id) selected @endif>
                                    {{ $balance->currency->symbol_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="category" class="col-md-4 col-form-label text-md-end">{{ __('Category') }}</label>
                    <div class="col-md-6">
                        <select name="category" id="category" class="form-control">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if ($record->category_id == $category->id) selected @endif>
                                    {{ $category->name }}</option>
                                @foreach ($category->subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}"
                                        @if ($record->category_id == $subcategory->id) selected @endif>
                                        &nbsp;&nbsp;{{ $subcategory->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
        @if ($record->type == 'Transfer')
        {{-- @dd($record) --}}
            <form action="{{ route('updateRecord', ['wallet' => $wallet->id,'record'=>$record]) }}" method="post" id="Transfer">
                @csrf
                @method('put')
                <div class="row mb-3">
                    <label for="sender" class="col-md-4 col-form-label text-md-end">{{ __('Sender') }}</label>
                    <div class="col-md-6">
                        <select name="sender" id="sender" class="form-control">
                            @foreach ($wallets as $w)
                                <option value="{{ $w->id }}" @if ($w->id == $record->transfer->sender_wallet) selected @endif>
                                    {{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="receiver" class="col-md-4 col-form-label text-md-end">{{ __('Receiver') }}</label>
                    <div class="col-md-6">
                        <select name="receiver" id="receiver" class="form-control">
                            @foreach ($wallets as $w)
                                <option value="{{ $w->id }}" @if ($w->id == $record->transfer->receiver_wallet)  selected @endif>{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="amount" class="col-md-4 col-form-label text-md-end">{{ __('Amount') }}</label>

                    <div class="col-md-6">
                        <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror"
                            name="amount" value="{{ old('amount') ?? $record->amount }}" required autocomplete="amount">

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
                        <input name="currency" type="hidden" id="currency" value="{{$record->balance->currency_id}}"/> 
                        <input  type="text" id="currency" class="form-control" value="{{$record->balance->currency->symbol_name}}" readonly/> 
                        {{-- <select name="currency" id="currency" class="form-control" readonly>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->symbol_name }}</option>
                            @endforeach
                        </select> --}}
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection
