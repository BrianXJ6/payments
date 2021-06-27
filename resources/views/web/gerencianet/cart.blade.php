@extends('layouts.app')
@section('subtitle', 'Pagamento via cartão')
@section('description', 'Descrição da tela Pagamento via cartão')
@section('keywords', 'Keywords da tela Pagamento via cartão')
@section('cover', asset(''))
@section('content')
<cart-comp></cart-comp>
@endsection
@push('scripts')
{{-- Homologação --}}
{{-- Para gerar a versão de produção acessar o painel dentro do gerencianet --}}
<script type='text/javascript'>
    var s=document.createElement('script');s.type='text/javascript';var v=parseInt(Math.random()*1000000);s.src='https://sandbox.gerencianet.com.br/v1/cdn/4ea5f52d78b41f2c66155e89b7c9c359/'+v;s.async=false;s.id='4ea5f52d78b41f2c66155e89b7c9c359';if(!document.getElementById('4ea5f52d78b41f2c66155e89b7c9c359')){document.getElementsByTagName('head')[0].appendChild(s);};$gn={validForm:true,processed:false,done:{},ready:function(fn){$gn.done=fn;}};
</script>
@endpush
