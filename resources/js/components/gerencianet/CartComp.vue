<template>
    <div class="container my-5">
        <h1>Pagamentos via cartão</h1>
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
                        <vue-numeric id="paymentForm.product.price" class="form-control form-control-sm" currency="R$" output-type="String" :precision="2" @change="getInstallments" v-model="paymentForm.product.price"></vue-numeric>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="paymentForm.product.qtd" class="m-0">Quantidade</label>
                        <vue-numeric id="paymentForm.product.qtd" class="form-control form-control-sm" output-type="String" @change="getInstallments" v-model="paymentForm.product.qtd"></vue-numeric>
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
            <fieldset class="border px-3 rounded mb-3">
                <legend class="w-auto h5 font-weight-bold">Dados do cartão</legend>
                <div class="form-row">
                    <div class="form-group col-sm-7">
                        <label for="paymentForm.card.number" class="m-0">Número do cartão</label>
                        <input id="paymentForm.card.number" class="form-control form-control-sm" type="tel" v-facade="'#### #### #### ####'" :value="paymentForm.card.number" @keyup="paymentForm.card.number = $event.target.unmaskedValue">
                    </div>
                    <div class="form-group col-sm-5">
                        <label for="paymentForm.card.brand" class="m-0">Bandeira do cartão</label>
                        <select id="paymentForm.card.brand" class="form-control form-control-sm" @change="getInstallments" v-model="paymentForm.card.brand">
                            <option value="">Selecione</option>
                            <option value="visa">Bandeira Visa</option>
                            <option value="mastercard">Bandeira MasterCard</option>
                            <option value="diners">Bandeira Dinners</option>
                            <option value="amex">Bandeira AmericanExpress</option>
                            <option value="elo">Bandeira Elo</option>
                            <option value="hipercard">Bandeira Hipercard</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="paymentForm.card.cvv" class="m-0">CVV</label>
                        <input id="paymentForm.card.cvv" class="form-control form-control-sm" type="tel" v-facade="'###'" :value="paymentForm.card.cvv" @keyup="paymentForm.card.cvv = $event.target.unmaskedValue">
                    </div>
                    <div class="form-group col-sm-5">
                        <label for="paymentForm.card.expiration_month" class="m-0">Mês de vencimento</label>
                        <select id="paymentForm.card.expiration_month" class="form-control form-control-sm" v-model="paymentForm.card.expiration_month">
                            <option value="">Selecione</option>
                            <option v-for="(month, i) in 12" :key="i" :value="month">{{ month }}</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-5">
                        <label for="paymentForm.card.expiration_year" class="m-0">Ano de vencimento</label>
                        <input id="paymentForm.card.expiration_year" class="form-control form-control-sm" type="tel" v-facade="'####'" :value="paymentForm.card.expiration_year" @keyup="paymentForm.card.expiration_year = $event.target.unmaskedValue">
                    </div>
                    <div class="form-group col-12">
                        <label for="paymentForm.card.installments" class="m-0">Parcelamento</label>
                        <select id="paymentForm.card.installments" class="form-control form-control-sm" :disabled="!installments.length" v-model="paymentForm.card.installments">
                            <option value="">Selecione</option>
                            <option v-for="(installment, i) in installments" :key="i" :value="installment.installment">
                                {{ installment.installment }}x de R${{ installment.currency }} ({{ parseFloat((installment.installment * installment.value / 100)).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}) }})
                            </option>
                        </select>
                    </div>
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
                gn: null,
                paymentFormLoader: false,
                installments: [],
                paymentForm: {
                    product: {
                        name: 'Produto teste',
                        price: '29.99',
                        qtd: '1',
                    },
                    user: {
                        email: 'email_cliente@servidor.com.br',
                        name: 'Gorbadoc Oldbuck',
                        cpf: '94271564656',
                        phone: '5144916523',
                    },
                    address: {
                        zip: '35400000',
                        street: 'Av. Paulista',
                        number: '10',
                        district: 'Bauxita',
                        city: 'Ouro Preto',
                        state: 'MG',
                    },
                    card: {
                        number: '4012001038443335',
                        brand: 'visa',
                        cvv: '123',
                        expiration_month: '12',
                        expiration_year: '2024',
                        installments: 1,
                        payment_token: '',
                    }
                }
            };
        },

        methods: {
            async sendPayment() {
                this.paymentFormLoader = true;
                await this.getPaymentToken();
                axios.post(`/api/payment/gnet/card`, this.paymentForm)
                    .then(() => alert('Pedido criado com sucesso!'))
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
            },

            getInstallments() {
                const price = parseFloat(this.paymentForm.product.price);
                const qtd = parseInt(this.paymentForm.product.qtd);
                const brand = this.paymentForm.card.brand;
                if (!price || !qtd || !brand || !this.gn) return;
                this.gn.getInstallments(Math.round(price * qtd * 100), this.paymentForm.card.brand, (error, response) => {
                    if (error) alert(error.error_description);
                    else this.installments = response.data.installments;
                });
            },

            getPaymentToken() {
                return new Promise((resolve, reject) => {
                    this.gn.getPaymentToken(this.paymentForm.card, (error, response) => {
                        if (error) {
                            alert(error.error_description);
                            this.paymentFormLoader = false;
                            reject;
                        } else {
                            this.paymentForm.card.payment_token = response.data.payment_token;
                            resolve();
                        }
                    });
                });
            }
        },

        mounted() {
            $gn.ready(gn => this.gn = gn);
        }
    }
</script>
