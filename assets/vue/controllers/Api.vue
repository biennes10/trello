<template>
  <div>
    <h1>Liste des projets connus</h1>
    <table class="item-table">
      <thead>
        <tr>
          <th>Auteur</th>
          <th>Projet</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id">
          <td>{{ item.name }}</td>
          <td>{{ item.description }}</td>
          <td><button class="btn btn-danger" @click="removeItem(item.id)">Supprimer</button></td>
        </tr>
      </tbody>
    </table>

    <h2 :style="{ 'margin-top': '10px'}">Ajouter un projet connu</h2>
    <form @submit.prevent="createItem">
      <div class="form-group">
        <label for="Auteur">Auteur du projet</label>
        <input type="text" v-model="newItemName" class="form-control" id="Auteur" aria-describedby="emailHelp" placeholder="Nom de l'auteur">
      </div>
      <div class="form-group">
        <label for="Projet">Nom du projet</label>
        <input type="text" v-model="newItemDescription" class="form-control" id="Projet" aria-describedby="emailHelp" placeholder="Nom de l'auteur">
      </div>

      <button :style="{ 'margin-top': '10px'}" class="btn btn-primary" type="submit">Create</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      items: [],
      newItemName: '',
      newItemDescription:''
    };
  },
  mounted() {
    this.fetchItems();
  },
  methods: {
    fetchItems() {
      axios.get('/projectApi/back/api/projects')
        .then(response => {
          this.items = response.data;
        })
        .catch(error => {
          console.error('Error fetching items:', error);
        });
    },
    removeItem(id) {
      axios.delete(`/projectApi/back/api/projects/delete/${id}`)
        .then(() => {
          this.items = this.items.filter(item => item.id !== id);
        })
        .catch(error => {
          console.error('Error removing item:', error);
        });
    },
    createItem() {
      axios.post('/projectApi/back/api/projects/create', { name: this.newItemName, description:this.newItemDescription })
        .then(response => {

          // Add the newly created item to the list
          this.items.push(response.data);
          // Clear the input field
          this.newItemName = '';
          this.newItemDescription = '';
        })
        .catch(error => {
          console.error('Error creating item:', error);
        });
    }
  }
};
</script>

<style>
.item-table {
  border-collapse: collapse;
  width: 100%;
}

.item-table th, .item-table td {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

.item-table th {
  background-color: #f2f2f2;
}
</style>