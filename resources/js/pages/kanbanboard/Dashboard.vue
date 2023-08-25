<template>
<div class="container">
    <Navbar></Navbar>
    <div class="row justify-content-center mt-5">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-center">Registered Users Information</div>

                <div class="card-body mt-3">
                    <div>
                        <h4>Click here to Register the new Users: <button type="button" class="btn insert_btn btn-danger mx-3" @click="openInsertDialog">
                                Insert</button></h4>
                    </div>

                    <table class="table table-bordered text-center mt-3">
                        <thead>
                            <th>S_no</th>
                            <th>name</th>
                            <th>email</th>
                            <th>password</th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                            <tr v-for="(user, index) in info" :key="user.id">
                                <td>{{ index + 1}}</td>
                                <td>{{user.name}}</td>
                                <td>{{user.email}}</td>
                                <td>{{user.password}}</td>
                                <td>
                                    <button type="button" class="btn edit_btn btn-info" @click="openEditDialog(index)">
                                        Edit</button>
                                    <button type="button" class="btn delete_btn btn-danger mx-3" @click="deleteInfo(index)">
                                        Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Dialog v-model:visible="ShareDialog" modal :header="dialogHeaderText" :style="{ width: '50vw' }">
                        <form @submit.prevent="insertOrUpdate" class="form mx-5">

                            <div class="row mb-3">
                                <label for="name">Name</label><br>
                                <div class="col-sm-8 my-3">
                                    <input type="text" class="form-control" id="name" v-model="name" required>
                                </div><br>

                                <label for="email">Email</label><br>
                                <div class="col-sm-8 my-3">
                                    <input type="email" class="form-control" id="email" v-model="email" required>
                                </div><br>

                                <label for="password">Password</label><br>
                                <div class="col-sm-8 my-3">
                                    <input type="password" class="form-control" id="password" v-model="password" required>
                                </div><br>
                            </div>

                            <button type="submit" class="btn btn-info">{{ action === 'insert' ? 'Register' : 'Update' }}</button>
                            <p class="mt-4 text-danger" v-if="error">{{ error }}</p>
                        </form>
                    </Dialog>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios';
import Navbar from './components/Navbar.vue';

export default {
    name: 'dashboard',

    components: {
          Navbar
    },

    data() {
        return {
            info: [],
            ShareDialog: false,
            action: "insert",
            action: "update",
            name: "",
            email: "",
            password: "",
            edit_info_id: null,
            edit_info_index: null,
            error: "",
            date: null,
        };
    },

    computed: {
        dialogHeaderText() {
            return this.action === "insert" ? "Register New User" : "Update the Registered User";
        },
    },

    mounted() {
        this.fetchUserInfo();
    },

    methods: {
        async fetchUserInfo() {
            try {
                const response = await axios.get('/api/info');
                this.info = response.data;
                console.log(response.data);
            } catch (error) {
                console.log(error);
            }
        },

        async insertOrUpdate() {
            console.log('insertOrUpdate method called');
            console.log('Action:', this.action);
            try {
                const requestData = {
                    id: this.edit_info_id,
                    name: this.name,
                    email: this.email,
                    password: this.password,
                };
                console.log('Request data:', requestData);

                if (this.action === "insert") {
                    const result = await axios.post('/api/infoInsert', requestData);

                    if (result.status === 200) {
                        this.fetchUserInfo();
                        this.ShareDialog = false;
                    }
                } else if (this.action === "update") {
                    const result = await axios.put('/api/infoUpdate/' + this.edit_info_id, requestData);

                    console.log('Update Result:', result, requestData);

                    if (result.status === 200) {
                        this.fetchUserInfo();
                        this.ShareDialog = false;
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.error = error.response.data.errors.email[0];
                } else {
                    this.error = 'An error occurred.';
                }
            }
        },

        deleteInfo(index) {
            console.log('Delete clicked for index:', index);
            if (this.info[index].id) {
                console.log('User ID:', this.info[index].id);
                axios.delete('/api/infoDelete/' + this.info[index].id)
                    .then(() => {
                        this.info.splice(index, 1);
                        console.log('User deleted successfully');
                        Swal.fire({
                            title: 'Success!',
                            text: 'User has been deleted successfully',
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    })
                    .catch((error) => {
                        console.log('Error deleting user:', error);
                    });
            }
        },

        openInsertDialog() {
            this.action = "insert";
            this.name = "";
            this.email = "";
            this.password = "";
            this.ShareDialog = true;
        },

        openEditDialog(index) {
            this.action = "update";
            const user = this.info[index];
            this.name = user.name;
            this.email = user.email;
            this.password = user.password;
            this.edit_info_id = user.id;
            this.edit_info_index = index;
            this.ShareDialog = true;
        },
    },
};
</script>
