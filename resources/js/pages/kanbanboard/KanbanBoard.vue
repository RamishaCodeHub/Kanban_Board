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
          <div class="input-group" v-if="!authenticated">
            <input
              type="text"
              class="form-control"
              v-model="column.newCardTitle"
              placeholder="Add a card"
            />
            <button class="btn btn-primary" @click="addCard(column)">+</button>
          </div>
        </div>
      </div>
      <div class="col-4" v-if="!authenticated">
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
      apiPath: '/api/cardStatus',
      authenticated: false, // authenticated flag
    };
  },

  mounted() {console.log(this.store)
    const email = this.$route.query.email;
    const token = this.$route.query.token;

    if (email && token) {
      // Fetch the data from the API using email and token
      this.fetchColumnsFromAPIWithEmail();
      this.authenticated = true; 
    } else {
      // Fetch the data from database
      this.fetchColumnsFromAPI(this.apiPath);
    }
  },

  methods: {
    fetchColumnsFromAPIWithEmail() {
      const email = this.$route.query.email;
      const token = this.$route.query.token;

      axios
        .get('/api/cardInfo', {
          params: {
            email: email,
            token: token,
          },
        })
        .then((response) => {
          this.columns = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    fetchColumnsFromAPI(apiPath) {
      axios
        .get(apiPath)
        .then((response) => {
          this.columns = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    addColumn() {
      // Add the column show only if the user is not authenticated
      if (!this.authenticated && this.newColumnTitle.trim() !== '') {
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

    addCard(column) {
      // Add the card only if the user is not authenticated
      if (!this.authenticated && column.newCardTitle.trim() !== '') {
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
      const { newIndex, oldIndex, item } = event;
      var card_id = event.item._underlying_vm_.id;
      const toColumn = this.columns.find((column, index) =>
        column.cards.find((card, index) => card.id === card_id)
      );

      this.updateCardPosition(toColumn.id, card_id);
    },
  },
};
</script>
