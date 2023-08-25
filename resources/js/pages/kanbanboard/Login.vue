<template>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card login">
                <div class="card-header text-center">Login Form</div>

                <div class="card-body">
                    <div class="input-group">
                        <form @submit.prevent="login_fun" class="form mx-5">

                            <div class="row mb-3">
                                <label for="email">Email</label><br>
                                <div class="col-sm-8 my-3">
                                    <input type="email" class="form-control" id="name" v-model="email" required>
                                </div><br>

                                <label for="passwrod">password</label><br>
                                <div class="col-sm-8 my-3">
                                    <input type="password" class="form-control" id="password" v-model="password" required>
                                </div><br>
                            </div>

                            <button type="submit" class="btn btn-info">Login</button>

                            <p class="mt-4 text-danger" v-if="error">{{ error }}</p>
                            <p class="mt-4 text-danger" v-if="emailError">{{ emailError }}</p>
                            <p class="mt-4 text-danger" v-if="passwordError">{{ passwordError }}</p>
                            <p class="mt-4 text-danger" v-if="unregisteredError">{{ unregisteredError }}</p>

                        </form>
                        <p class="mt-4 mx-5">
                            Don't have an account?
                            <router-link to="/register">Register</router-link>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</template>

    
<script>
export default {
    name: 'login',
    data() {
        return {
            email: '',
            password: '',
            error: '',
            emailError: '',
            passwordError: '',
            unregisteredError: '',
            roleId: '',
        };
    },
    mounted() {
        this.email = ''; // Reset email field to empty
        this.password = ''; // Reset password field to empty
    },

    methods: {
        async login_fun() {
            try {
                const response = await axios.post("/api/login", {
                    email: this.email,
                    password: this.password,
                });
                console.warn(response);
                if (response.status === 200) {
                    this.$store.commit('setIsLoggedIn', true);
                    if (response.data.roleId === 1) {
                        this.$router.push("/dashboard");
                    } else if (response.data.roleId === 2) {
                        this.$router.push("/kanban");
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    if (errors.email) {
                        this.emailError = errors.email[0];
                    } else if (errors.password) {
                        this.passwordError = errors.password[0];
                    } else if (errors.unregistered) {
                        this.unregisteredError = errors.unregistered[0];
                    } else {
                        this.error = "Invalid email or password";
                    }
                } else {
                    this.error = "Invalid user, try again";
                }
            }
        },
    }
};
</script>
