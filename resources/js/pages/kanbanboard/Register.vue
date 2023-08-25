<template>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center font-weight-bold">Registration Form</div>

                <div class="card-body">
                    <div class="input-group">
                        <form @submit.prevent="register_fun" class="form mx-5">

                            <div class="row mb-3">
                                <label for="name">Name</label><br>
                                <div class="col-sm-8 my-3">
                                    <input type="text" class="form-control" id="name" v-model="name" required>
                                </div><br>

                                <label for="email">Email</label><br>
                                <div class="col-sm-8 my-3">
                                    <input type="email" class="form-control" id="name" v-model="email" required>
                                </div><br>

                                <label for="passwrod">password</label><br>
                                <div class="col-sm-8 my-3">
                                    <input type="password" class="form-control" id="password" v-model="password" required>
                                </div><br>
                            </div>

                            <button type="submit" class="btn btn-info">Register</button>
                            <p class="mt-4 text-danger" v-if="error">{{ error }}</p>
                        </form>
                        <p class="mt-4 mx-5">
                            Have an account?
                            <router-link to="/">Login</router-link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

  
<script>
import axios from 'axios';

export default {
    name: 'register',
    data() {
        return {
            name: '',
            email: '',
            password: '',
            error: ''
        };
    },

    methods: {
        async register_fun() {
            try {
                let result = await axios.post("/api/register", {
                    name: this.name,
                    email: this.email,
                    password: this.password
                });

                if (result.status === 200) {
                    this.$router.push("/");
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.error = error.response.data.errors.email[0];
                } else {
                    this.error = 'An error occurred during registration.';
                }
            }
        }
    },

    
};
</script>
