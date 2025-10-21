<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\DiscountEngine;
use Illuminate\Http\Request;

class DiscountApiController extends Controller
{
    public function __construct(private DiscountEngine $engine) {}

    // Preview diskon otomatis (tier) dari subtotal
    public function previewAuto(Request $request)
    {
        $data = $request->validate([
            'subtotal' => ['required','numeric','min:0'],
        ]);

        $auto = $this->engine->computeAutoDiscount((float)$data['subtotal']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'auto_discount' => $auto, // amount, scheme, tier
            ],
        ]);
    }

    // Validasi voucher + hold redemption
    public function validateVoucher(Request $request)
    {
        $data = $request->validate([
            'code' => ['required','string','max:100'],
            'subtotal' => ['required','numeric','min:0'],
            'customer_ref' => ['nullable','string','max:100'],
        ]);

        $userId = $request->user()->id;

        $res = $this->engine->validateAndHoldVoucher(
            $data['code'],
            (float)$data['subtotal'],
            $userId,
            $data['customer_ref'] ?? null
        );

        if (!$res['ok']) {
            return response()->json([
                'status' => 'error',
                'message' => $res['reason'],
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'voucher' => $res['voucher'],
                'amount'  => $res['amount'],
                'redemption_id' => $res['redemption_id'],
                'hold_expires_in_minutes' => $res['hold_expires_in_minutes'],
            ],
        ]);
    }

    // Void/batalkan held redemption (mis. user batal pakai voucher)
    public function voidHeld(Request $request)
    {
        $data = $request->validate([
            'redemption_id' => ['required','integer','min:1'],
        ]);

        $ok = $this->engine->voidHeld((int)$data['redemption_id']);

        return $ok
            ? response()->json(['status' => 'success'])
            : response()->json(['status' => 'error', 'message' => 'Tidak bisa membatalkan.'], 422);
    }
}


