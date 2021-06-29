<?php

namespace App\Http\Controllers\API\Gnet;

use App\Models\Order;
use Illuminate\Http\Request;
use Gerencianet\Gerencianet;
use App\Http\Controllers\Controller;

class CallbackController extends Controller {

    public function __invoke(Request $request) {
        $request->validate(['notification' => ['nullable', 'string']]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://tls.testegerencianet.com.br/");
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_exec($ch);
        echo "\n";

        if ($err = curl_error($ch)) {
            var_dump($err);
            echo "DEBUG INFORMATION:\n###########\n";
            echo "CURL VERSION:\n";
            echo json_encode(curl_version(), JSON_PRETTY_PRINT);
        }

        if (!$request->notification) return response()->json([]);

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
            $notification = $chargeNotification['data'][count($chargeNotification['data']) - 1];
            $paid_at      = $notification['created_at'];
            $newStatus    = $notification['status']['current'];
            $payment_id   = $notification['identifiers']['charge_id'];

            // Atualizando o status do pedido
            $order          = Order::where('payment_id', $payment_id)->firstOrFail();
            $order->status  = $newStatus;

            // Marcar como pago
            if ($newStatus == 'paid' || $newStatus == 'settled')
                $order->paid_at = $paid_at;

            // Remover informações de pagamento inicial
            if ($newStatus == 'paid' || $newStatus == 'settled' || $newStatus == 'canceled')
                $order = $this->removeExtraData($order);

            // Atualizar informações do pedido.
            $order->save();

            // Deletar pedido se for cancelado
            if ($newStatus == 'canceled') $order->delete();

            return response()->json(true, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    protected function removeExtraData($order) {
        $order->billet_barcode = null;
        $order->billet_link    = null;
        $order->billet_pdf     = null;
        $order->expire_at      = null;
        $order->imagemQrcode   = null;
        $order->qrcode         = null;
        return $order;
    }
}
