<?php

namespace App\Http\Controllers\API\Gnet;

use App\Models\Order;
use Gerencianet\Gerencianet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gerencianet\Exception\GerencianetException;

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
            'zipcode' => $request->address['zip'],
            'street' => $request->address['street'],
            'number' => $request->address['number'],
            'neighborhood' => $request->address['district'],
            'city' => $request->address['city'],
            'state' => $request->address['state'],
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

        // Produtos referente a compra
        $items = [
            ['name' => $request->product['name'], 'amount' => 1, 'value' => intval((float) $request->product['price'] * 100)]
        ];

        try {
            // Iniciando API Gerencianet
            $api = new Gerencianet([
                'client_id'     => config('gateway.gnet_client_id'),
                'client_secret' => config('gateway.gnet_client_secret'),
                'sandbox'       => true,
            ]);

            // Executando metodo de pagamento (one step)
            $pay_charge = $api->oneStep([], [
                'items'   => $items,
                'payment' => $payment,
                'metadata' => array('notification_url' => url('api/payment/gnet/status')),
            ]);

            // Criando pedido
            $order                       = new Order();
            $order->payment_id           = $pay_charge['data']['charge_id'];
            $order->payment_type         = $pay_charge['data']['payment'];
            $order->status               = $pay_charge['data']['status'];
            $order->total                = (float) $pay_charge['data']['total'] / 100;
            $order->products             = json_encode($items);
            $order->user_email           = $request->user['email'];
            $order->user_name            = $request->user['name'];
            $order->user_document        = $request->user['cpf'];
            $order->user_phone           = $request->user['phone'];
            $order->user_birth           = '1990-12-10';
            $order->address_zip          = $request->address['zip'];
            $order->address_street       = $request->address['street'];
            $order->address_district     = $request->address['district'];
            $order->address_city         = $request->address['city'];
            $order->address_state        = $request->address['state'];
            $order->card_last_for_digits = substr($request->card['number'], -4);
            $order->card_brand           = $request->card['brand'];
            $order->installments         = $pay_charge['data']['installments'];
            $order->installment_value    = (float) $pay_charge['data']['installment_value'] / 100;
            $order->save();
            return response()->json(true, 201);
        } catch (GerencianetException $e) {
            dd($e);
            // dd($e->error);
            // dd($e->errorDescription);
        } catch (\Exception $e) {
            dd($e);
            // dd($e->getMessage());
        }
    }
}