<template>
    <div class="search">
        <h2>Search Bar</h2>
        <Dialog v-model:visible="ShareDialog" :style="{ width: '50vw' }">
            <form class="form mx-5">

                <div class="row mb-3">
                    <label for="name">Name</label><br>
                    <div class="col-sm-8 my-3">
                        <input type="text" class="form-control" id="name" v-model="name" required>
                    </div><br>
                    </div>

        
            </form>
        </Dialog>
    </div>
</template>

<script>

export default{
    name: 'search',
  
    data(){
        return{
            ShareDialog: false,
        }
    }
}

</script>