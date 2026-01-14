<?php

namespace App\Http\Controllers;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Bank::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required|string',
        'logo' => 'nullable|string',
    ]);

    $bank = Bank::create([
        'name' => $request->name,
        'logo' => $request->logo,
    ]);

    return response()->json($bank, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        return $bank;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $bank->update($request->all());
        return $bank;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
         $bank->delete();
        return response()->noContent();
    }
}
