<?php

return [
    'gnet_ip' => env('GNET_IP', '34.193.116.226'),

    /*
    |--------------------------------------------------------------------------
    | Cliente ID / Cliente Secret
    |--------------------------------------------------------------------------
    |
    | Aqui será necessário setar as credênciais do Cliente ID e Cliente Secret fornecidas pelo Gerencianet na api: https://sistema.gerencianet.com.br/api/minhas-aplicacoes/141436/(producao / homologacao)
    | tenha ciência de usar a chave correta para ambiente de Produção ou Homologação (teste)
    |
    */
    'gnet_client_id'     => env('GNET_CLIENT_ID', ''),
    'gnet_client_secret' => env('GNET_CLIENT_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Gerencianet config
    |--------------------------------------------------------------------------
    |
    | Informe a API para receber disparo do webhook referente a atualização de estatus de transações;
    | Informe o ambiente sandbox com verdadeiro ou falso
    |
    */
    'gnet_webhook' => env('GNET_WEBHOOK', ''),
    'gnet_sandbox' => env('GNET_SANDBOX', true),
];
