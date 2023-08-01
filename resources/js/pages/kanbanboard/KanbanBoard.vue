<template>
    <div class="container">
        <SideNavbar></SideNavbar>
        <div class="row">
            <div class="col-4" v-for="(column, columnIndex) in columns" :key="columnIndex">
                <div class="card-body mt-5">
                    <div class="card-header">{{ column.title }}</div>
                    <draggable class="card-body" :list="column.cards" group="kanban" @end="handleDragEnd" itemKey="index">
                        <template #item="{ element, index }">
                            <div class="card-item" :key="index">
                                <div class="card-item-body">{{ element.name }}</div>
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
                        <input type="text" class="form-control" v-model="newColumnTitle" placeholder="Add list Title" />
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
                apiKey: 'b9286b0dc514f0c15eee9fdacc745e65',
                token: 'ATTA416aca972258d877fdc7116f6c77f9031760925a32a52f29795641be44282587D3B8F865',
                boardId: '64be2e36202321eb5c6412da',
            };
        },
    
        mounted() {
            this.fetchAllData();
        },
    
        methods: {
            fetchBoard() {
                return fetch(`https://api.trello.com/1/boards/${this.boardId}?key=${this.apiKey}&token=${this.token}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error('Error fetching board:', error);
                    });
            },
    
            fetchLists() {
                return fetch(`https://api.trello.com/1/boards/${this.boardId}/lists?key=${this.apiKey}&token=${this.token}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .catch(err => {
                        console.error('Error fetching lists:', err);
                    });
            },
    
            fetchCards(listId) {
                return fetch(`https://api.trello.com/1/lists/${listId}/cards?key=${this.apiKey}&token=${this.token}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error('Error fetching cards:', error);
                    });
            },
    
            fetchAllData() {
                const board = this.fetchBoard();
                const lists = this.fetchLists();
    
                Promise.all([board, lists])
                    .then(([boardData, listsData]) => {
    
                        this.boardInfo = JSON.stringify(boardData, null, 2);
                        this.columns = listsData.map(list => {
                            return {
                                id: list.id,
                                title: list.name,
                                cards: [],
                                newCardTitle: '',
                            };
                        });
    
                        // Fetch and populate cards for each list
                        const fetchCardPromises = this.columns.map(column =>
                            this.fetchCards(column.id).then(cardsData => {
                                column.cards = cardsData;
                            })
                        );
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            },
    
            addCardToTrello(column, title) {
                if (title.trim() !== '') {
                    const cardData = {
                        name: title,
                        idList: column.id,
                        key: this.apiKey,
                        token: this.token,
                    };
    
                    return fetch('https://api.trello.com/1/cards', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(cardData),
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed to add card to Trello');
                            }
                            return response.json();
                        })
                        .catch(error => {
                            console.error('Error adding card to Trello:', error);
                        });
                }
            },
    
            addColumnToTrello(title) {
                if (title.trim() !== '') {
                    const listData = {
                        name: title,
                        idBoard: this.boardId,
                        key: this.apiKey,
                        token: this.token,
                    };
    
                    return fetch('https://api.trello.com/1/lists', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(listData),
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed to add column to Trello');
                            }
                            return response.json();
                        })
                        .catch(error => {
                            console.error('Error adding column to Trello:', error);
                        });
                }
            },
    
            addCard(column) {
                if (column.newCardTitle.trim() !== '') {
                    this.addCardToTrello(column, column.newCardTitle)
                        .then(newCardData => {
                            column.cards.push(newCardData);
                            column.newCardTitle = '';
                        });
                }
            },
    
            addColumn() {
                if (this.newColumnTitle.trim() !== '') {
                    this.addColumnToTrello(this.newColumnTitle)
                        .then(newColumnData => {
                            this.columns.push({
                                id: newColumnData.id,
                                title: newColumnData.name,
                                cards: [],
                                newCardTitle: '',
                            });
                            this.newColumnTitle = '';
                        });
                }
            },
    
            // Update the card position in the database
            updateCardPosition(columnId, card_id, position) {
                const cardData = {
                    idList: columnId,
                    pos: position,
                    key: this.apiKey,
                    token: this.token,
                };
    
                return fetch(`https://api.trello.com/1/cards/${card_id}?key=${this.apiKey}&token=${this.token}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(cardData),
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
    