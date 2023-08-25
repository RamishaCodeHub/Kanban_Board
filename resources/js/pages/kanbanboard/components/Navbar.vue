<template>
<div class="card main-body flex justify-content-end">
    <Sidebar v-model:visible="visible">
        <h4 class="text-center">Admin Panel</h4>
        <div class="list mt-5">
            <router-link to="/kanban" class="list-group-item list-group-item-action py-2 ripple">
                <i class="fas fa-tachometer-alt fa-fw me-3"></i><span>Main dashboard</span>
              </router-link><br>

            <router-link to="/dashboard" class="list-group-item list-group-item-action py-2 ripple" aria-current="true">
                <i class="fas fa-users fa-fw me-3"></i><span>User Info</span>
            </router-link><br>
            
            <span class="p-float-label">
                <Calendar v-model="date" showIcon />
                <router-view :selectedDate="date" />
            </span><br> 
            
            <router-link to="/chat" class="list-group-item list-group-item-action py-2 ripple" aria-current="true">
                <i class="fas fa-sms fa-fw me-3"></i><span>chat</span>
            </router-link><br>
        </div>
    </Sidebar>
    <div class="button-container">
        <Button icon="fa fa-bars" @click="visible = true" />
        <button class="btn btn-danger" @click="logout">Logout</button>
    </div>
</div>
</template>

<script>
export default {
    name: 'Navbar',

    data() {
        return {
            visible: false,
            date:null,
        };
    },

    mounted() {},

    methods: {
        logout() {
            axios.get('/api/logout')
                .then(response => {
                    this.$store.commit('logout');
                    this.$router.push('/');
                })
                .catch(error => {
                    console.error(error);
                });
        },

    },
};
</script>
