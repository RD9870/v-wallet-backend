<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\TopUp;

class TopUpController extends Controller
{
    public function store(Request $request)
    {
        //
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'accountNumber' => 'required|string',
            'pin' => 'required|string',
            'amount' => 'required|integer|min:1',
        ]);

        $response = Http::get('https://696369282d146d9f58d368f8.mockapi.io/users');

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Bank service unavailable'
            ], 503);
        }

        $bankUsers = $response->json();


        $bankAccount = collect($bankUsers)
            ->firstWhere('accountNumber', $request->accountNumber);

        if (!$bankAccount) {
            return response()->json([
                'message' => 'Invalid account number'
            ], 400);
        }

        if ($bankAccount['PIN'] !== $request->pin) {
            return response()->json([
                'message' => 'Invalid PIN'
            ], 400);
        }

        if ($bankAccount['balance'] < $request->amount) {
            return response()->json([
                'message' => 'Insufficient bank balance'
            ], 400);
        }


        $user = $request->user();


        DB::transaction(function () use ($request, $bankAccount, $user) {

            $user->increment('balance', $request->amount);

            TopUp::create([
                'user_id' => $user->id,
                'bank_id' => $request->bank_id,
                'bank_account_number' => $bankAccount['accountNumber'],
                'amount' => $request->amount,
            ]);
        });

        return response()->json([
            'message' => 'Top up successful',
            'new_balance' => $user->fresh()->balance
        ]);
    }
}
