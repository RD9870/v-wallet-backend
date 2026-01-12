<?php

namespace App\Http\Controllers;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use App\Models\Transfer;
use App\Models\TransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Requests\GenerateQrRequest;
use App\Http\Requests\PhoneTransferRequest;
use App\Http\Requests\QrTransferRequest;
class TransfersController extends Controller
{

public function generateQr(GenerateQrRequest $request)
{
   $data= $request->validated();
    $sender = auth()->user();
    if ($sender->balance < $data['amount']) {
        return response()->json(['error' => 'Insufficient balance'], 400);
    }
    $qrRequest = TransferRequest::create([
        'id' => Str::uuid(),
        'sender_id' => $sender->id,
        'receiver_id' => null,
        'amount' => $data['amount'],
        'status' => 'pending',
        'expires_at' => Carbon::now()->addMinutes(1),
    ]);

    $qrData = json_encode([
        'sender_id' => $sender->id,
        'amount' => $data['amount'],
        'qr_request_id' => $qrRequest->id
    ]);

    return response()->json([
        'qr_id' => $qrRequest->id,
        'qr_data' => $qrData,
        'expires_at' => $qrRequest->expires_at
    ]);
}
public function phoneTransfers(PhoneTransferRequest $request)
{
    $data = $request->validated();
    $sender = auth()->user();
    try {
        return DB::transaction(function () use ($data, $sender) {

            $receiver = User::where('phone', $data['receiver_phone'])
                ->lockForUpdate()
                ->first();

            if ($sender->id === $receiver->id) {
                return response()->json([
                    'message' => 'Sender and receiver cannot be the same'
                ], 400);
            }

            if ($sender->balance < $data['amount']) {
                return response()->json([
                    'message' => 'Insufficient balance'
                ], 400);
            }

            $sender->decrement('balance', $data['amount']);
            $receiver->increment('balance', $data['amount']);

            $transfer = TransferRequest::create([
                'id'          => Str::uuid(),
                'sender_id'   => $sender->id,
                'receiver_id' => $receiver->id,
                'amount'      => $data['amount'],
                'status'      => 'completed',
                'expires_at'  => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Transfer completed successfully',
                'data'    => $transfer,
            ], 200);

        });

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

public function QrTransfers(QrTransferRequest $request)
{
        $data = $request->validated();


    $receiver = auth()->user();

    $qrRequest = TransferRequest::find($data['qr_request_id']);

    if ($qrRequest->status !== 'pending') {
        return response()->json(['error' => 'This QR has already been used or expired'], 400);
    }

    if ($qrRequest->expires_at < now()) {
        $qrRequest->update(['status' => 'expired']);
        return response()->json(['error' => 'This QR has expired'], 400);
    }

    $sender = User::find($qrRequest->sender_id);

    if ($sender->balance < $qrRequest->amount) {
        return response()->json(['error' => 'Sender has insufficient balance'], 400);
    }

    $sender->decrement('balance', $qrRequest->amount);
    $receiver->increment('balance', $qrRequest->amount);

    $qrRequest->update([
        'receiver_id' => $receiver->id,
        'status' => 'completed',
    ]);

    return response()->json([
        'message' => 'Transfer successful',
        'sender_balance' => $sender->balance,
        'receiver_balance' => $receiver->balance
    ]);
}



}
