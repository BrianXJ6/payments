<?php

namespace App\Http\Controllers\API\Gnet;

use App\Models\Order;
use Illuminate\Http\Request;
use Gerencianet\Gerencianet;
use App\Http\Controllers\Controller;

class PixController extends Controller {

    public function __invoke(Request $request) {
        // Produtos referente a compra e total do pedido.
        $total_pedido = 0;
        $items = [
            ['name' => $request->product['name'], 'amount' => (int) $request->product['qtd'], 'value' => intval((float) $request->product['price'] * 100)],
        ];
        foreach ($items as $item) $total_pedido += (float) (($item['value'] / 100) * $item['amount']);

        try {
            // Iniciando API Gerencianet
            $api = Gerencianet::getInstance([
                'client_id'     => config('gateway.gnet_client_id'),
                'client_secret' => config('gateway.gnet_client_secret'),
                'pix_cert'      => base_path('') . config('gateway.gnet_pix_cert'),
                'sandbox'       => config('gateway.gnet_sandbox'),
            ]);

            // Executando metodo de pagamento via PIX
            $pix = $api->pixCreateImmediateCharge([], [
                'calendario'         => ['expiracao' => 86400], // 24 horas em segundos
                'valor'              => ['original' => (string) $total_pedido],
                'chave'              => '5581997160910', // Chave pix da conta Gerencianet do recebedor
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
            $order->total            = $total_pedido;
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
