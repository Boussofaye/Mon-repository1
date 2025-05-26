<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PayTechController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $response = Http::withHeaders([
            'API_KEY' => env('PAYTECH_API_KEY'),
            'API_SECRET' => env('PAYTECH_API_SECRET'),
            'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
        ])->asForm()->post('https://paytech.sn/api/payment/request-payment', [
            'item_name'     => $request->item_name,
            'item_price'    => $request->item_price,
            'command_name'  => "Paiement " . $request->item_name,
            'ref_command'   => Str::uuid(),
            'env'           => 'test', // mets 'prod' si tu es en production
            'currency'      => 'XOF',
            'ipn_url'       => url('/paytech/ipn'),
            'success_url'   => url('/paytech/success'),
            'cancel_url'    => url('/paytech/cancel'),
            'custom_field'  => json_encode(['from' => 'Flutter']),
        ]);

        $data = $response->json();

        if (isset($data['token'])) {
            return response()->json([
                'redirect_url' => 'https://paytech.sn/payment/checkout/' . $data['token']
            ]);
        } else {
            return response()->json([
                'error' => $data['error'] ?? 'Erreur inconnue'
            ], 400);
        }
    }

    public function paymentSuccess()
    {
        return response()->json(['message' => 'Paiement effectué avec succès']);
    }

    public function paymentCancel()
    {
        return response()->json(['message' => 'Paiement annulé']);
    }

    public function ipn(Request $request)
    {
        // Gère la notification PayTech ici (mise à jour DB, etc.)
        return response()->json(['message' => 'Notification IPN reçue']);
    }
}