<template>
<div class="card main-body flex justify-content-end">
    <Sidebar v-model:visible="visible">
        <h4 class="flex justify-content-center">Select Any Color To Change Background Color</h4>
        <div class="flex justify-content-center">
            <p>Selected color code: {{ selectedColor }}</p>
            <input type="color" v-model="selectedColor"/>
        </div>
        <hr>
        <h4>Select Image to change background</h4><br>
        <div>
            <input type="file" @change="onFileChange" /> <br><br>
            <div v-if="imagePreview">
                <h3>Image Preview:</h3>
                <img :src="imagePreview" alt="Preview" />
            </div>
            <button class="btn btn-primary mt-4" @click="uploadImage" :disabled="!selectedFile">Submit</button>
        </div>
    </Sidebar>
    <Button icon="pi pi-cog" @click="visible = true" />
</div>
</template>

<script>
export default {
    name: 'SideNavbar',

    data() {
        return {
            visible: false,
            selectedColor: '#dbcadb',
            selectedFile: null,
            backgroundImage: '',
            imagePreview: null,
        };
    },

    mounted() {
        // Get the background color from the database
        axios
            .get('/api/setting/background-color')
            .then((response) => {
                const color = response.data.value;
                this.selectedColor = color;
                this.updateBackgroundColor(color);
            })
            .catch((error) => {
                console.log(error);
            });

        // Get the background image from the database
        axios
            .get('/api/setting/background-image')
            .then((response) => {
                const imagePath = response.data.value;
                if (imagePath) {
                    this.backgroundImage = imagePath;
                    this.setBackgroundImage(imagePath);
                }
            })
            .catch((error) => {
                console.log(error);
            });
    },

    methods: {
        updateBackgroundColor(color) {
            axios
                .post('/api/setting/background-color', {
                    value: color,
                })
                .then((response) => {
                    // Update the background color of all .card-body elements
                    const cardBodies = document.querySelectorAll('.card-body');
                    cardBodies.forEach((cardBody) => {
                        cardBody.style.backgroundColor = color;
                    });
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        setBackgroundImage(imagePath) {
            // Update the background image
            this.backgroundImage = imagePath;
            var newvari = 'storage/images/' + imagePath;
            document.querySelector('.main-body').style.backgroundImage = `url(${newvari})`;
        },

        onFileChange(event) {
            this.selectedFile = event.target.files[0];
            console.log(this.selectedFile);

            if (this.selectedFile) {
                const reader = new FileReader();
                reader.onload = () => {
                    this.imagePreview = reader.result;
                    document.querySelector('.main-body').style.backgroundImage = `url(${reader.result})`;
                };
                reader.readAsDataURL(this.selectedFile);
            }
        },

        uploadImage() {
            if (this.selectedFile) {
                const formData = new FormData();
                formData.append('image', this.selectedFile);

                axios
                    .post('/api/setting/background-image', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    })
                    .then((response) => {
                        const imagePath = response.data.value;
                        this.setBackgroundImage(imagePath);
                        this.updateBackgroundColor(this.selectedColor);
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            }
        },
    },
};
</script>

