<?php

return [

    'gnet_webhook' => env('GNET_WEBHOOK', null),

    /*
    |--------------------------------------------------------------------------
    | Cliente ID / Cliente Secret
    |--------------------------------------------------------------------------
    |
    | Aqui será necessário setar as credênciais do Cliente ID e Cliente Secret fornecidas pelo Gerencianet na api: https://sistema.gerencianet.com.br/api/minhas-aplicacoes/141436/(producao / homologacao)
    | tenha ciência de usar a chave correta para ambiente de Produção ou Homologação (teste)
    |
    */
    'gnet_client_id'     => env('GNET_CLIENT_ID', null),
    'gnet_client_secret' => env('GNET_CLIENT_SECRET', null),
];
