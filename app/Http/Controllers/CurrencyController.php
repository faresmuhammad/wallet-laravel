<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = Currency::all();
        return view('currency.index')->with('currencies', $currencies);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-currency');
        return view('currency.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-currency');
        Currency::create([
            'name' => $request->get('name'),
            'pfx_symbol' => $request->get('pfx'),
            'sfx_symbol' => $request->get('sfx'),
            'unit_name' => $request->get('unit'),
            'cent_name' => $request->get('cent'),
            'scale' => $request->get('scale'),
            'symbol_name' => $request->get('symbol_name'),
        ]);
        return redirect('/currencies');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        Gate::authorize('update-currency');
        return view('currency.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        Gate::authorize('update-currency');
        $currency->update([
            'name' => $request->get('name'),
            'pfx_symbol' => $request->get('pfx'),
            'sfx_symbol' => $request->get('sfx'),
            'unit_name' => $request->get('unit'),
            'cent_name' => $request->get('cent'),
            'scale' => $request->get('scale'),
            'symbol_name' => $request->get('symbol_name'),
        ]);
        return redirect('/currencies');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        Gate::authorize('delete-currency');
        $currency->delete();
        return redirect('/currencies');
    }
}
