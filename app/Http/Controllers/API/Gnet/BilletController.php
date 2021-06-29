<?php

namespace App\Http\Controllers\API\Gnet;

use App\Models\Order;
use Illuminate\Http\Request;
use Gerencianet\Gerencianet;
use App\Http\Controllers\Controller;

class BilletController extends Controller {

    public function __invoke(Request $request) {
        // Dados do cliente
        $customer = [
            'name'         => $request->user['name'],
            'cpf'          => $request->user['cpf'],
            'phone_number' => $request->user['phone'],
            'email'        => $request->user['email'],
            'birth'        => '1990-12-10',
        ];

        // Desconto se aplicável
        // $discount = [
        //     'type'  => 'currency', // tipo de desconto a ser aplicado
        //     'value' => 599 // valor de desconto
        // ];

        // Desconto condicional se aplicável
        // $conditional_discount = [
        //     'type'       => 'percentage', // seleção do tipo de desconto
        //     'value'      => 500, // porcentagem de desconto
        //     'until_date' => '2019-08-30' // data máxima para aplicação do desconto
        // ];

        // Dados do pagamento
        $payment = [
            'banking_billet' => [
                'expire_at'               => now()->addDays(3)->toDateString(),
                'message'                 => 'Mensagem de teste linha 01\nMensagem de teste linha 02',
                'customer'                => $customer,
                // 'discount'             => $discount,
                // 'conditional_discount' => $conditional_discount
            ]
        ];

        // Produtos referente a compra
        $items = [
            ['name' => $request->product['name'], 'amount' => (int) $request->product['qtd'], 'value' => intval((float) $request->product['price'] * 100)],
        ];

        try {
            // Iniciando API Gerencianet
            $api = new Gerencianet([
                'client_id'     => config('gateway.gnet_client_id'),
                'client_secret' => config('gateway.gnet_client_secret'),
                'sandbox'       => config('gateway.gnet_sandbox')
            ]);

            // Executando metodo de pagamento (one step)
            $pay_charge = $api->oneStep([], [
                'items'    => $items,
                'payment'  => $payment,
                'metadata' => array('notification_url' => config('gateway.gnet_webhook')),
            ]);
            if ($pay_charge['code'] != 200) return response()->json(['message' => $pay_charge['error_description']['message']], 422);

            // Criando pedido
            $order                   = new Order();
            $order->payment_id       = $pay_charge['data']['charge_id'];
            $order->payment_type     = $pay_charge['data']['payment'];
            $order->status           = $pay_charge['data']['status'];
            $order->total            = (float) $pay_charge['data']['total'] / 100;
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
            $order->billet_barcode   = $pay_charge['data']['barcode'];
            $order->billet_link      = $pay_charge['data']['link'];
            $order->billet_pdf       = $pay_charge['data']['pdf']['charge'];
            $order->expire_at        = $pay_charge['data']['expire_at'];
            $order->save();

            return response()->json([
                'barcode'   => $order->billet_barcode,
                'link'      => $order->billet_link,
                'pdf'       => $order->billet_pdf,
                'expire_at' => $order->expire_at,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
