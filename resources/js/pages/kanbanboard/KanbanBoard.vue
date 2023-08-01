<template>
    <div class="container">
        <SideNavbar></SideNavbar>
        <div class="row">
            <div class="col-4" v-for="(column, columnIndex) in columns" :key="columnIndex">
                <div class="card mt-5">
                    <div class="card-header">{{ column.title }}</div>
                    <draggable class="card-body" :list="column.cards" group="kanban" @end="handleDragEnd" itemKey="index">
                        <template #item="{ element, index }">
                            <div class="card-item" :key="index">
                                <div class="card-item-body">{{ element.title }}</div>
                            </div>
                        </template>
                    </draggable>
                    <div class="input-group">
                        <input type="text" class="form-control" v-model="column.newCardTitle" placeholder="Add a card" />
                        <button class="btn btn-primary" @click="addCard(column)">+</button>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card mt-5">
                    <div class="card-header">
                        <input type="text" class="form-control" v-model="newColumnTitle" placeholder="Add list Tittle" />
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary" @click="addColumn">Add Another List</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </template>
    
    <script>
    import SideNavbar from './components/SideNavbar.vue';
    import draggable from 'vuedraggable';
    import axiosInstance from 'axios';
    
    
    export default {
        name: 'KanbanBoard',
        components: {
            draggable,
            SideNavbar,
        },
    
        data() {
            return {
                columns: [],
                newColumnTitle: '',
            };
        },
    
        mounted() {
            this.fetchColumnsFromAPI(); //Fetch the data From the Database
        },
    
        methods: {
    
            fetchColumnsFromAPI() {
                axios
                    .get('/api/cardStatus')
                    .then((response) => {
                        this.columns = response.data;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
    
            // add the cards in database
    
            addCard(column) {
                if (column.newCardTitle.trim() !== '') {
                    const card = {
                        title: column.newCardTitle,
                        column_id: column.id,
                    };
                    axios
                        .post('/api/cards', card)
                        .then((response) => {
                            column.cards.push(response.data);
                            column.newCardTitle = '';
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            },
    
            // create the new column in database
    
            addColumn() {
                if (this.newColumnTitle.trim() !== '') {
                    const column = {
                        title: this.newColumnTitle,
                        cards: [],
                        newCardTitle: '',
                    };
    
                    axios
                        .post('/api/cardStatus', column)
                        .then((response) => {
                            this.columns.push(response.data);
                            this.newColumnTitle = '';
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            },
    
            // Update the card position in the database
            updateCardPosition(columnId, card_id) {
                axios
                    .put(`/api/cards/${card_id}`, {
                        status_id: columnId,
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
    
            handleDragEnd(event) {
                const {
                    newIndex,
                    oldIndex,
                    item
                } = event;
                var card_id = event.item._underlying_vm_.id;
                const toColumn = this.columns.find((column, index) =>
                    column.cards.find((card, index) => card.id === card_id)
                );
    
                this.updateCardPosition(toColumn.id, card_id);
            },
        },
    };
    </script>
    