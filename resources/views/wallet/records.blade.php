@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('newrecord', ['wallet' => $wallet->id]) }}" class="btn btn-primary">New Record</a>
        @foreach ($records as $record)
            <div class="px-3 py-2 my-2 d-flex justify-content-between align-items-center text-white"
                style="background: @if ($record->type == 'Expense') #ff6666 @elseif($record->type == 'Income') #00b400 @else #0016a7 @endif;
                  border-radius: 10px">
                <span>{{ $record->currency->pfx_symbol }} {{ $record->amount }} {{ $record->currency->sfx_symbol }}</span>
                <div>
                    <a href="{{ route('editRecord', ['wallet' => $wallet->id, 'record' => $record->id]) }}"
                        class="btn me-2 btn-warning">Edit</a>
                    <form action="{{ route('deleteRecord', ['wallet' => $wallet->id, 'record' => $record->id]) }}"
                        style="display: inline;" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
