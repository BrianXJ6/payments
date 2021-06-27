<?php

namespace App\Http\Controllers\API\Gnet;

use App\Models\Order;
// use Gerencianet\Auth;
use Illuminate\Http\Request;
use Gerencianet\Gerencianet;
use App\Http\Controllers\Controller;

class PixController extends Controller {

    public function __invoke(Request $request) {
        // Produtos referente a compra
        $items = [
            ['name' => $request->product['name'], 'amount' => 1, 'value' => intval((float) $request->product['price'] * 100)]
        ];

        try {
            //generate Access Token
            // $auth = new Auth([
            //     'client_id'     => config('gateway.gnet_client_id'),
            //     'client_secret' => config('gateway.gnet_client_secret'),
            //     'pix_cert'      => base_path('') . '/producao-305206-glpets.pem', // caminho do certificado
            // ]);
            // $auth->authorize();

            // dd($auth);

            // Iniciando API Gerencianet
            $api = Gerencianet::getInstance([
                'client_id'        => 'Client_Id_abfae467f76613079ea5493151fc7c862036c9fa',
                'client_secret'    => 'Client_Secret_47aa6e0136e35b2becea163548f3d073f1f201bb',
                // 'client_id'     => config('gateway.gnet_client_id'),
                // 'client_secret' => config('gateway.gnet_client_secret'),
                'pix_cert'         => base_path('') . '/producao-305206-glpets.pem', // caminho do certificado
                // 'sandbox'       => false,
            ]);

            // Executando metodo de pagamento via PIX
            $pix = $api->pixCreateImmediateCharge([], [
                'calendario'         => ['expiracao' => 86400], // 24 horas em segundos
                'valor'              => ['original' => $request->product['price']],
                'chave'              => '+5581997160910', // Chave pix da conta Gerencianet do recebedor
                'solicitacaoPagador' => 'Informe o número ou identificador do pedido.',
            ]);
            if (!isset($pix['txid'])) return response()->json(['message' => $pix['mensagem']], 422);

            // Gerando QR Code
            $qrcode = $api->pixGenerateQRCode(['id' => $pix['loc']['id']]);

            // Criando pedido
            $order                   = new Order();
            $order->payment_id       = $pix['loc']['id'];
            $order->payment_type     = 'pix';
            $order->status           = $pix['status'];
            $order->total            = (float) $pix['valor']['original'];
            $order->products         = json_encode($items);
            $order->user_email       = $request->user['email'];
            $order->user_name        = $request->user['name'];
            $order->user_document    = $request->user['cpf'];
            $order->user_phone       = $request->user['phone'];
            $order->user_birth       = '1990-12-10';
            $order->address_zip      = $request->address['zip'];
            $order->address_street   = $request->address['street'];
            $order->address_district = $request->address['district'];
            $order->address_city     = $request->address['city'];
            $order->address_state    = $request->address['state'];
            $order->txid             = $pix['txid'];
            $order->qrcode           = $qrcode['qrcode'];
            $order->imagemQrcode     = $qrcode['imagemQrcode'];
            $order->expire_at        = now()->addHours(24)->toDateString();
            $order->save();
            return response()->json([
                'qrcode'       => $order->qrcode,
                'imagemQrcode' => $order->imagemQrcode,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
