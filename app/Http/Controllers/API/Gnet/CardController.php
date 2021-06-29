<?php

namespace App\Http\Controllers\API\Gnet;

use App\Models\Order;
use Gerencianet\Gerencianet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CardController extends Controller {

    public function __invoke(Request $request) {
        // Dados do cliente
        $customer = [
            'name'         => $request->user['name'],
            'cpf'          => $request->user['cpf'],
            'phone_number' => $request->user['phone'],
            'email'        => $request->user['email'],
            'birth'        => '1990-12-10',
        ];

        // Dados de cobrança
        $billingAddress = [
            'zipcode'      => $request->address['zip'],
            'street'       => $request->address['street'],
            'number'       => $request->address['number'],
            'neighborhood' => $request->address['district'],
            'city'         => $request->address['city'],
            'state'        => $request->address['state'],
        ];

        // Dados do pagamento
        $payment = [
            'credit_card' => [
                'customer'        => $customer,
                'installments'    => $request->card['installments'],
                'billing_address' => $billingAddress,
                'payment_token'   => $request->card['payment_token'],
            ]
        ];

        // Produtos referente a compra e total do pedido.
        $total_pedido = 0;
        $items = [
            ['name' => $request->product['name'], 'amount' => (int) $request->product['qtd'], 'value' => intval((float) $request->product['price'] * 100)],
        ];
        foreach ($items as $item) $total_pedido += (float) (($item['value'] / 100) * $item['amount']);

        try {
            // Iniciando API Gerencianet
            $api = new Gerencianet([
                'client_id'     => config('gateway.gnet_client_id'),
                'client_secret' => config('gateway.gnet_client_secret'),
                'sandbox'       => config('gateway.gnet_sandbox'),
            ]);

            // Executando metodo de pagamento (one step)
            $pay_charge = $api->oneStep([], [
                'items'    => $items,
                'payment'  => $payment,
                'metadata' => array('notification_url' => config('gateway.gnet_webhook')),
            ]);
            if ($pay_charge['code'] != 200) return response()->json(['message' => $pay_charge['error_description']['message']], 422);

            // Criando pedido
            $order                         = new Order();
            $order->payment_id             = $pay_charge['data']['charge_id'];
            $order->payment_type           = $pay_charge['data']['payment'];
            $order->status                 = $pay_charge['data']['status'];
            $order->total                  = $total_pedido;
            $order->products               = json_encode($items);
            $order->user_email             = $request->user['email'];
            $order->user_name              = $request->user['name'];
            $order->user_document          = $request->user['cpf'];
            $order->user_phone             = $request->user['phone'];
            $order->user_birth             = '1990-12-10';
            $order->address_zip            = $request->address['zip'];
            $order->address_street         = $request->address['street'];
            $order->address_district       = $request->address['district'];
            $order->address_city           = $request->address['city'];
            $order->address_state          = $request->address['state'];
            $order->card_last_for_digits   = substr($request->card['number'], -4);
            $order->card_brand             = $request->card['brand'];
            $order->card_installments      = $pay_charge['data']['installments'];
            $order->card_installment_value = (float) $pay_charge['data']['installment_value'] / 100;
            $order->card_charge_total      = (float) $pay_charge['data']['total'] / 100;
            $order->save();
            return response()->json([], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
