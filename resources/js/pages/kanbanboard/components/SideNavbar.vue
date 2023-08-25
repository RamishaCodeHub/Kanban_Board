<template>
<div class="card main-body flex justify-content-end">
    <Sidebar v-model:visible="visible">
        <h4 class="flex justify-content-center">Select Any Color To Change Background Color</h4>
        <div class="flex justify-content-center">
            <p>Selected color code: {{ selectedColor }}</p>
            <input type="color" v-model="selectedColor" />
        </div>
        <hr>
        <h4>Select Image to change background</h4><br>
        <div>
            <!-- search the image -->
            <h5>Search Image</h5>
            <form @submit.prevent="searchImages">
                <div class="form-group">
                    <input type="text" class="form-control" v-model="searchQuery" placeholder="Search for images">
                </div><br>
                <div class="form-group">
                    <button type="submit" class="btn btn-danger">Search</button>
                </div>
            </form> <br><br>

            <div class="row">
                <div class="col-md-12">
                    <div id="images">
                        <div class="image-card" v-for="image in searchResults" :key="image.id" @click="selectBackgroundImage(image.src.original)">
                            <img :src="image.src.small" :alt="image.photographer" width="200" height="200"><br><br>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="imagePreview">
                <h3>Image Preview:</h3>
                <img :src="imagePreview" alt="Preview" height="200" width="200" />
            </div>
            <button class="btn btn-primary mt-4" @click="uploadImage">Submit</button>
        </div>
        <button class="btn btn-primary mt-5" @click="logout" v-if="!$route.query.email && !$route.query.token">Logout</button>
    </Sidebar>
    <div class="button-container">
        <Button icon="pi pi-cog" @click="visible = true" />
        <Button label="Share" icon="pi pi-external-link" @click="ShareDialog = true" v-if="!$route.query.email && !$route.query.token" />

        <Dialog v-model:visible="ShareDialog" modal header="Share Board" :style="{ width: '50vw' }">
            <form>
                <div class="row mb-3">
                    <label for="email">Email</label><br>
                    <div class="col-sm-8 my-3">
                        <input type="email" class="form-control" id="email" placeholder="Email address" v-model="email" required>
                    </div>
                </div>
                <button class="btn btn-primary" @click.prevent="sendEmail">Send Email</button>
            </form>
        </Dialog>
    </div>
</div>
</template>

<script>
export default {
    name: 'SideNavbar',

    data() {
        return {
            visible: false,
            ShareDialog: false,
            selectedColor: '#dbcadb',
            selectedFile: null,
            backgroundImage: '',
            imagePreview: null,
            email: '',
            kanbanPageUrl: '',
            pexelsApiKey: 'nryjOjFaRByXnDepSk11OThK5esuJN3uUEgPL9cZaG1VAmYG92KR9Urd',
            searchQuery: '',
            searchResults: [],
        };
    },

    mounted() {

        const pathArray = window.location.pathname.split('/');
        const kanbanPageUrl = `${pathArray.slice(0, pathArray.length - 1).join('/')}/kanban`;
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
            .get('/api/setting/getbackground-image')
            .then((response) => {
                const imageUrl = response.data.value;
                if (imageUrl) {
                    this.backgroundImage = imageUrl;
                    this.setBackgroundImage(imageUrl);
                }
            })
            .catch((error) => {
                console.log(error);
            });
    },

    methods: {

        sendEmail() {
            // Fetch the signed Kanban URL from the backend
            axios
                .get("/api/generate-kanban-url", {
                    params: {
                        email: this.email,
                        kanbanPageUrl: this.kanbanPageUrl,
                    },
                })
                .then((response) => {
                    // Once you receive the signed URL, include it in the email content
                    const kanbanPageUrl = response.data.url;

                    axios
                        .post("/api/send-email", {
                            email: this.email,
                        })
                        .then((response) => {
                            console.log("Email sent successfully:", response.data);
                            this.ShareDialog = false;
                        })
                        .catch((error) => {
                            console.error("Error sending email:", error);
                        });
                })
                .catch((error) => {
                    console.error("Error fetching signed Kanban URL:", error);
                });
        },

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

        setBackgroundImage(imageUrl) {
            this.backgroundImage = imageUrl;
            var newvari = imageUrl;
            document.querySelector('.main-body').style.backgroundImage = `url(${newvari})`;
            // Set the image preview
            this.imagePreview = imageUrl;
        },

        selectBackgroundImage(imageUrl) {
            console.log('Selected Image URL:', imageUrl);

            // Store the selected Pexels image URL in the database
            axios
                .post('/api/setting/background-image', {
                    value: imageUrl,
                })
                .then((response) => {
                    console.log('Image URL stored in database:', response.data);

                    // Update the background image
                    this.setBackgroundImage(imageUrl);
                    const smallImageUrl = imageUrl.replace('/original/', '/small/');
                    this.imagePreview = smallImageUrl;
                })
                .catch((error) => {
                    console.log('Error storing image URL in database:', error);
                });
        },

        onFileChange(event) {
            this.selectedFile = event.target.files[0];
            console.log('selected image:', this.selectedFile);

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
            console.log('Upload image method called');
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
                        console.log('Image upload response:', response.data);
                        const imageUrl = response.data.value;

                        this.setBackgroundImage(imageUrl);
                        this.updateBackgroundColor(this.selectedColor);
                        this.visible = false;
                    })
                    .catch((error) => {
                        console.log('Image upload error', error);
                    });
            } else {
                // If the image is not selected update background color only
                this.updateBackgroundColor(this.selectedColor);
                this.visible = false;
            }
        },

        searchImages() {
            const apiUrl = `https://api.pexels.com/v1/search?query=${this.searchQuery}&per_page=10`;
            const headers = {
                Authorization: `${this.pexelsApiKey}`,
            };

            fetch(apiUrl, {
                    headers
                })
                .then(response => response.json())
                .then(data => {
                    this.searchResults = data.photos;

                    if (this.searchResults.length > 0) {
                        const imageUrl = this.searchResults[0].src.original;
                        this.setBackgroundImage(imageUrl);
                    }
                })
                .catch(error => {
                    console.error('Error searching images:', error);
                });
        },

        logout() {
            axios.get('/api/logout')
                .then(response => {
                    this.$store.commit('logout');
                    this.$router.push({
                        name: 'login'
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
};
</script>
