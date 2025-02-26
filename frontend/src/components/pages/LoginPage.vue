<template>
    <div class="center-sign">
        <a href="https://www.okler.net/" class="logo float-left">
            <img src="img/logo.png" height="70" alt="Porto Admin" />
        </a>

        <div class="panel card-sign">
            <div class="card-title-sign mt-3 text-end">
                <h2 class="title text-uppercase font-weight-bold m-0"><i
                        class="bx bx-user-circle me-1 text-6 position-relative top-5"></i> Sign In</h2>
            </div>
            <div class="card-body">
                <form @submit.prevent>
                    <div class="form-group mb-3">
                        <label>Username</label>
                        <div class="input-group">
                            <input name="username" type="text" class="form-control form-control-lg"
                                v-model="user.username" />
                            <span class="input-group-text">
                                <i class="bx bx-user text-4"></i>
                            </span>
                        </div>
                        <p v-if="error.username" style="color: red">{{ error.username }}</p>
                    </div>

                    <div class="form-group mb-3">
                        <div class="clearfix">
                            <label class="float-left">Password</label>
                            <a href="pages-recover-password.html" class="float-end">Lost Password?</a>
                        </div>
                        <div class="input-group">
                            <input name="pwd" type="password" class="form-control form-control-lg"
                                v-model="user.password" />
                            <span class="input-group-text">
                                <i class="bx bx-lock text-4"></i>
                            </span>
                        </div>
                        <p v-if="error.password" style="color: red">{{ error.password }}</p>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-primary mt-2" @click="userLogin">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <p class="text-center text-muted mt-3 mb-3">&copy; Copyright 2021. All Rights Reserved.</p>
    </div>
</template>
<script>
import axios from 'axios';
export default {
    data() {
        return {
            user: {
                username: "admin@gmail.com",
                password: "123456",
            },
            error: {
                username: "",
                password: "",
                user: ""
            },
        };
    },
    methods: {
        async userLogin() {
            await axios.post('http://127.0.0.1:8000/api/login', {
                grant_type: 'password',
                client_id: '1',
                client_secret: 'JLMkwIHvaT7C7QJhvXVn9iXtp4cABDKiAxRWbGR7',
                username: this.user.username,
                password: this.user.password,
                scope: ''
            }).then(response => {
                this.error = {};
                if (response.data.status && response.data.status === "error") {
                    this.error = response.data.data;
                } else {
                    localStorage.setItem('active-user', JSON.stringify(response.data));

                    this.$router.push('/');
                }
            }).catch(error => {
                this.error = {};
                this.error.user = error;
            });
        },
    },
};
</script>