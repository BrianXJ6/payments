<?php

namespace App\Http\Controllers\API\Gnet;

use App\Models\Order;
use Illuminate\Http\Request;
use Gerencianet\Gerencianet;
use App\Http\Controllers\Controller;

class CallbackController extends Controller {

    public function __invoke(Request $request) {
        $request->validate(['notification' => ['required', 'string']]);
        try {
            // Iniciando API Gerencianet
            $api = new Gerencianet([
                'client_id'     => config('gateway.gnet_client_id'),
                'client_secret' => config('gateway.gnet_client_secret'),
                'sandbox'       => config('gateway.gnet_sandbox'),
            ]);

            // Executando consulta de status (notification)
            $chargeNotification = $api->getNotification(['token' => $request->notification], []);
            if ($chargeNotification['code'] !== 200) return response()->json(['message' => 'Não foi possível atender essa solicitação'], 422);

            // Obtendo status e identificação do pedido
            $lastNotification = $chargeNotification['data'][count($chargeNotification['data']) - 1];
            $created_at       = $lastNotification['created_at'];
            $newStatus        = $lastNotification['status']['current'];
            $payment_id       = $lastNotification['identifiers']['charge_id'];

            // Atualizando o status do pedido
            $order          = Order::where('payment_id', $payment_id)->firstOrFail();
            $order->status  = $newStatus;
            if ($newStatus == 'paid' || $newStatus == 'settled') $order->paid_at = $created_at;
            $order->save();
            return response()->json(true, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
