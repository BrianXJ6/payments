<template>
    <div class="container my-5">
        <h1>Pagamentos via PIX</h1>
        <hr>
        <form @submit.prevent="sendPayment">
            <fieldset class="border px-3 rounded mb-3">
                <legend class="w-auto h5 font-weight-bold">Dados da compra</legend>
                <div class="form-row">
                    <div class="form-group col-sm-5">
                        <label for="paymentForm.product.name" class="m-0">Nome do produto</label>
                        <input id="paymentForm.product.name" class="form-control form-control-sm" type="text" v-model="paymentForm.product.name">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="paymentForm.product.price" class="m-0">Preço do produto</label>
                        <vue-numeric id="paymentForm.product.price" class="form-control form-control-sm" currency="R$" output-type="String" :precision="2" v-model="paymentForm.product.price"></vue-numeric>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="paymentForm.product.qtd" class="m-0">Quantidade</label>
                        <vue-numeric id="paymentForm.product.qtd" class="form-control form-control-sm" output-type="String" v-model="paymentForm.product.qtd"></vue-numeric>
                    </div>
                </div>
            </fieldset>
            <fieldset class="border px-3 rounded mb-3">
                <legend class="w-auto h5 font-weight-bold">Dados do cliente</legend>
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="paymentForm.user.email" class="m-0">E-mail</label>
                        <input id="paymentForm.user.email" class="form-control form-control-sm" type="email" v-model="paymentForm.user.email">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="paymentForm.user.name" class="m-0">Nome completo</label>
                        <input id="paymentForm.user.name" class="form-control form-control-sm" type="text" v-model="paymentForm.user.name">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="paymentForm.user.cpf" class="m-0">CPF</label>
                        <input id="paymentForm.user.cpf" class="form-control form-control-sm" type="tel" v-facade="'###.###.###-##'" :value="paymentForm.user.cpf" @keyup="paymentForm.user.cpf = $event.target.unmaskedValue">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="paymentForm.user.phone" class="m-0">Telefone</label>
                        <input id="paymentForm.user.phone" class="form-control form-control-sm" type="tel" v-facade="['(##) #####-####', '(##) ####-####']" :value="paymentForm.user.phone" @keyup="paymentForm.user.phone = $event.target.unmaskedValue">
                    </div>
                </div>
            </fieldset>
            <fieldset class="border px-3 rounded mb-3">
                <legend class="w-auto h5 font-weight-bold">End. de cobrança</legend>
                <div class="form-row">
                    <div class="form-group col-sm-3">
                        <label for="paymentForm.address.zip" class="m-0">CEP</label>
                        <input id="paymentForm.address.zip" class="form-control form-control-sm" type="tel" v-facade="'#####-###'" :value="paymentForm.address.zip" @keyup="searchZip">
                    </div>
                    <div class="form-group col-sm-7">
                        <label for="paymentForm.address.street" class="m-0">Logradouro</label>
                        <input id="paymentForm.address.street" class="form-control form-control-sm" type="text" v-model="paymentForm.address.street">
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="paymentForm.address.number" class="m-0">Número</label>
                        <input id="paymentForm.address.number" class="form-control form-control-sm" type="text" v-model="paymentForm.address.number">
                    </div>
                    <div class="form-group col-sm-5">
                        <label for="paymentForm.address.district" class="m-0">Bairro</label>
                        <input id="paymentForm.address.district" class="form-control form-control-sm" type="text" v-model="paymentForm.address.district">
                    </div>
                    <div class="form-group col-sm-5">
                        <label for="paymentForm.address.city" class="m-0">Cidade</label>
                        <input id="paymentForm.address.city" class="form-control form-control-sm" type="text" v-model="paymentForm.address.city">
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="paymentForm.address.state" class="m-0">UF</label>
                        <input id="paymentForm.address.state" class="form-control form-control-sm" type="tel" v-facade="'AA'" :value="paymentForm.address.state" @keyup="paymentForm.address.state = $event.target.unmaskedValue">
                    </div>
                </div>
            </fieldset>
            <fieldset v-if="pix.status" class="border px-3 rounded mb-3">
                <legend class="w-auto h5 font-weight-bold">PIX</legend>
                <div class="text-center">
                    <img :src="pix.imagemQrcode" class="img-fluid">
                    <p>{{ pix.qrcode }}</p>
                </div>
            </fieldset>
            <div class="text-right">
                <div v-if="paymentFormLoader" class="spinner-border align-middle mr-2"></div>
                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill" :disabled="paymentFormLoader">Finalizar pagamento</button>
            </div>
        </form>
    </div>
</template>

<script>
    import VueNumeric from 'vue-numeric';
    import { facade } from 'vue-input-facade';
    export default {
        directives: { facade },
        components: { VueNumeric },

        data: function () {
            return {
                paymentFormLoader: false,
                pix: { status: false, qrcode: '', imagemQrcode: '' },
                paymentForm: {
                    product: {
                        name: 'Produto teste PIX',
                        price: '00.01',
                        qtd: '1',
                    },
                    user: {
                        email: 'brianferreira15@hotmail.com',
                        name: 'Brian Ferreira de Barros',
                        cpf: '08093996443',
                        phone: '81997160910',
                    },
                    address: {
                        zip: '54740660',
                        street: 'Rua Expedicionário Constantino Avelino de Sá',
                        number: '242',
                        district: 'Capibaribe',
                        city: 'São Lourenço da Mata',
                        state: 'PE',
                    }
                }
            };
        },

        methods: {
            async sendPayment() {
                this.paymentFormLoader = true;
                axios.post(`/api/payment/gnet/pix`, this.paymentForm)
                    .then(response => {
                        alert('Pedido criado com sucesso!');
                        this.pix = { status: true, qrcode: response.data.qrcode, imagemQrcode: response.data.imagemQrcode };
                    })
                    .catch(err => error.log(err.response))
                    .finally(() => this.paymentFormLoader = false);
            },

            searchZip(e) {
                const zip = this.paymentForm.address.zip = e.target.unmaskedValue;
                if (!zip || zip.length != 8) return;
                const newAxios = axios.create({ baseURL: 'https://api.postmon.com.br/v1/cep/', headers: null });
                newAxios.get(zip).then(response => {
                    const { logradouro, bairro, cidade, estado } = response.data;
                    this.paymentForm.address = Object.assign(this.paymentForm.address, { street: logradouro, district: bairro, city: cidade, state: estado });
                });
            }
        }
    }
</script>
