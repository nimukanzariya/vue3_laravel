<template>
    <div class="row">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <div class="card-actions">
                        <a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
                        <a href="#" class="card-action card-action-dismiss" data-card-dismiss=""></a>
                    </div>

                    <h2 class="card-title">Create new Stock</h2>
                </header>
                <div class="card-body">
                    <form class="form-horizontal form-bordered" @submit.prevent>
                        <div class="form-group row pb-4">
                            <label class="col-lg-3 control-label text-lg-end pt-2" for="name">Name</label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" id="name" v-model="stock.name">
                                <p v-if="error.name" style="color: red">{{ error.name }}</p>
                            </div>
                        </div>
                        <div class="form-group row pb-4">
                            <label class="col-lg-3 control-label text-lg-end pt-2" for="price">Price</label>
                            <div class="col-lg-6">
                                <input type="number" class="form-control" id="price" v-model="stock.price">
                                <p v-if="error.price" style="color: red">{{ error.price }}</p>
                            </div>
                        </div>
                        <div class="form-group row pb-4">
                            <label class="col-lg-3 control-label text-lg-end pt-2" for="quantity">Quantity</label>
                            <div class="col-lg-6">
                                <input type="number" class="form-control" id="quantity" v-model="stock.quantity">
                                <p v-if="error.quantity" style="color: red">{{ error.quantity }}</p>
                            </div>
                        </div>
                        <div class="form-group row pb-4">
                            <label class="col-lg-3 control-label text-lg-end pt-2" for="store">Store</label>
                            <div class="col-lg-6">
                                <select class="form-control" v-model="stock.store" id="storeSelect">
                                    <option value="">Select store</option>
                                    <option v-for="store in stores" :key="store" :value="store">
                                        {{ store }}
                                    </option>
                                </select>
                                <p v-if="error.store" style="color: red">{{ error.store }}</p>
                            </div>
                        </div>
                        <div class="form-group row pb-4">
                            <div class="col-sm-12 text-end">
                                <button type="button" class="btn btn-primary mt-2" @click="SaveData">Save Data</button>
                            </div>
                        </div>

                    </form>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import { ref } from 'vue'

export default {
    data() {
        return {
            stock: {
                name: "",
                price: "",
                quantity: "",
                store: "",
            },
            error: {
                name: "",
                price: "",
                quantity: "",
                store: "",
            },
        };
    },
    setup() {
        const stores = ref([]);
        const token = JSON.parse(localStorage.getItem("active-user"))?.access_token;

        const fetchStores = async () => {
            try {
                const response = await axios.get("http://127.0.0.1:8000/api/stores", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    }
                });
                stores.value = response.data.data;
            } catch (error) {
                console.error("Error fetching users:", error);
            }
        };
        fetchStores(fetchStores);

        return { stores };
    },
    methods: {
        async SaveData() {
            const token = JSON.parse(localStorage.getItem("active-user"))?.access_token;

            await axios.post('http://127.0.0.1:8000/api/stocks',
                {
                    name: this.stock.name,
                    price: this.stock.price,
                    quantity: this.stock.quantity,
                    store: this.stock.store
                },
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json",
                    }
                }
            ).then(response => {
                console.log(response);
                this.error = {};
                if (response.data.status && response.data.status === "error") {
                    this.error = response.data.data;
                } else {
                    this.$router.push('/');
                }
            }).catch(error => {
                this.error = {};
                this.error = error;
            });
        },
    },
};

</script>