@extends('layouts.app')

@section('content')
    <div class="container">
        @can('create-currency')
            <a href="/currencies/create" class="btn btn-primary">Create Currency</a>
        @endcan
        <div id="currency" class="table-bordered">
            <table class="table">
                <thead>
                <tr>
                    <th class="col-1">Id</th>
                    <th class="col-1">Name</th>
                    <th class="col-1">PFX Symbol</th>
                    <th class="col-1">SFX Symbol</th>
                    <th class="col-1">Unit Name</th>
                    <th class="col-1">Cent Name</th>
                    <th class="col-1">Scale</th>
                    <th class="col-1">Symbol Name</th>
                    @can('update-currency','delete-currency')
                        <th class="col-2">Control</th>
                    @endcan
                </tr>
                </thead>
                @foreach($currencies as $currency)
                    <tr>

                        <td>{{$currency->id}}</td>
                        <td>{{$currency->name}}</td>
                        <td>{{$currency->pfx_symbol}}</td>
                        <td>{{$currency->sfx_symbol}}</td>
                        <td>{{$currency->unit_name}}</td>
                        <td>{{$currency->cent_name}}</td>
                        <td>{{$currency->scale}}</td>
                        <td>{{$currency->symbol_name}}</td>
                        @can('update-currency','delete-currency')

                            <td>
                                <div class="control">

                                    <a href="/currencies/{{$currency->id}}/edit" class="btn btn-warning">Edit</a>
                                    <form action="/currencies/{{$currency->id}}" style="display: inline;" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        @endcan
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
