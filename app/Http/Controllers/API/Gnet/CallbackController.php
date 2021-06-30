<?php

namespace App\Http\Controllers\API\Gnet;

use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Gerencianet\Gerencianet;
use App\Http\Controllers\Controller;

class CallbackController extends Controller {

    public function __invoke(Request $request) {
        $request->validate([
            'notification'     => ['nullable', 'string'],
            'pix'              => ['nullable', 'array'],
            'pix.*.endToEndId' => ['required', 'string'],
            'pix.*.txid'       => ['required', 'string'],
        ]);

        // Validando servidores GNET.
        // if (config('gateway.gnet_sandbox') === false && strcmp($request->ip(), config('gateway.gnet_ip')) !== 0)
        // return response()->json(['message' => 'Ação não autorizada!'], 422);

        // Processar callback API PIX
        if ($request->pix) return $this->cbPix($request->pix);

        // Processar callback API GNET
        if ($request->notification) return $this->cbGnet($request->notification);

        // Confirmação de resposta caso não seja uma notificação de pagamento.
        return response()->json([], 200);
    }

    protected function cbPix($pixRequest) {
        try {
            // Iniciando API Gerencianet
            $api = Gerencianet::getInstance([
                'client_id'     => config('gateway.gnet_client_id'),
                'client_secret' => config('gateway.gnet_client_secret'),
                'pix_cert'      => base_path('') . config('gateway.gnet_pix_cert'),
                'sandbox'       => config('gateway.gnet_sandbox'),
            ]);

            // Separando o ultimo callback via PIX recebido
            $pixRequest = Arr::last($pixRequest);

            // Executando consulta de status (PIX)
            $pix = $api->pixDetailCharge(['txid' => $pixRequest['txid']]);
            if (isset($pix['nome']) || empty($pix['pix'])) throw new \Exception('Não foi possível atender essa solicitação');

            // Obtendo o ultimo registro de pagamento via PIX
            $lastPix = Arr::last($pix['pix']);

            // Validando registro com request
            // if (count(array_diff($pixRequest, $lastPix))) return response()->json(['message' => 'Não foi possível atender essa solicitação'], 400);
            // if (count(array_diff($pixRequest, $lastPix))) throw new \Exception('Não foi possível atender essa solicitação');

            // Atualizando informação do pedido
            $order               = Order::where('txid', $pix['txid'])->firstOrFail();
            $order->total_paid   = (!$order->total_paid) ? $lastPix['valor'] : ($order->total_paid +  $lastPix['valor']);
            $order->paid_at      = $lastPix['horario'];
            $order->status       = $pix['status'];
            $order->qrcode       = null;
            $order->imagemQrcode = null;
            $order->expire_at    = null;
            $order->save();
            return response()->json();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], ($e->getCode()) ?: 400);
        }
    }

    protected function cbGnet($notification) {
        try {
            // Iniciando API Gerencianet
            $api = new Gerencianet([
                'client_id'     => config('gateway.gnet_client_id'),
                'client_secret' => config('gateway.gnet_client_secret'),
                'sandbox'       => config('gateway.gnet_sandbox'),
            ]);

            // Executando consulta de status (notification)
            $chargeNotification = $api->getNotification(['token' => $notification], []);
            if ($chargeNotification['code'] !== 200) throw new \Exception('Não foi possível atender essa solicitação');

            // Obtendo dados da ultima notificação
            $notification = Arr::last($chargeNotification['data']);

            // Atualizando informação do pedido
            $order                 = Order::where('payment_id', $notification['identifiers']['charge_id'])->firstOrFail();
            $order->total_paid     = isset($notification['value']) ? ($notification['value'] / 100) : null;
            $order->status         = $notification['status']['current'];
            $order->billet_barcode = null;
            $order->billet_link    = null;
            $order->billet_pdf     = null;
            $order->expire_at      = null;

            // Marcar data do pagamento
            if ($order->status == 'paid' || $order->status == 'settled')
                $order->paid_at = now();

            // Atualizar informações do pedido.
            $order->save();
            return response()->json();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], ($e->getCode()) ?: 400);
        }
    }
}
