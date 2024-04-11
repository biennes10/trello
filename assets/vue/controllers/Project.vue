<template>
    <div v-if="project">
    <div>
    <h1>{{ project.name }}</h1>

    <form @submit.prevent="createCategory">
        <div class="form-group">
            <label for="inputCategorie">Nouvelle catégorie</label>
            <input type="text" v-model="newCategoryName" class="form-control" id="inputCategorie" aria-describedby="emailHelp" placeholder="Entrer nom de la catégorie">
        </div>
      <button class="btn btn-primary" type="submit" id="btnCatgorie" :style="{ 'margin-top': '10px'}">Créer categorie</button>
    </form>
    
    <div :style="{ 'margin-top': '30px'}" v-for="category in project.categories" :key="category.id">
      <h2>{{ category.name }}</h2>
      <form @submit.prevent="createTicket(category.id)">
        <div class="form-group">
            <label for="inputTicketName">Nom du ticket</label>
            <input v-model="newTicketName" required type="text" class="form-control" id="inputTicketName" aria-describedby="emailHelp" placeholder="Nom du ticket">
        </div>
        <div class="form-group">
            <label for="inputTicketDescription">Description du ticket</label>
            <input v-model="newTicketDescription" required type="text" class="form-control" id="inputTicketDescription" aria-describedby="emailHelp" placeholder="Nom du ticket">
        </div>

        <button :style="{ 'margin-top': '20px'}" class="btn btn-secondary" type="submit">Créer un ticket</button>
      </form>
      <ul :style="{ 'margin-top': '20px'}" >
        <li v-for="ticket in category.tickets" :key="ticket.id">
         <strong>Nom:</strong> {{ ticket.name }}  <strong>Description:</strong> {{ticket.description}}
          <button class="btn btn-danger" @click="deleteTicket(category.id, ticket.id)">Supprimer</button>
        </li>
      </ul>
    </div>
  </div>
    </div>
    <div v-else>
      <p>Loading...</p>
    </div>
  </template>
  
  <script>
import axios from 'axios';

export default {
  props: {
    projectId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      project: null,
      newCategoryName: '',
      newTicketName: '',
      newTicketDescription: ''
    };
  },
  mounted() {
    this.fetchProject();
  },
  methods: {
    async fetchProject() {
      try {
        const response = await axios.get(`/project/api/${this.projectId}`);
        this.project = response.data;
      } catch (error) {
        console.error('Error fetching project:', error);
      }
    },
    async createCategory() {
      try {
        const response = await axios.post(`/project/api/${this.projectId}/categories`, {
          name: this.newCategoryName
        });
        // Refresh project data after category creation
        this.fetchProject();
        // Clear the new category name input
        this.newCategoryName = '';
      } catch (error) {
        console.error('Error creating category:', error);
      }
    },
    async createTicket(categoryId) {
      try {
        const response = await axios.post(`/project/api/${this.projectId}/categories/${categoryId}/tickets`, {
          name: this.newTicketName,
          description: this.newTicketDescription
        });
        this.fetchProject();
        this.newTicketName = '';
        this.newTicketDescription = '';
      } catch (error) {
        console.error('Error creating ticket:', error);
      }
    },
    async deleteTicket(categoryId, ticketId) {
      try {
        const response = await axios.delete(`/project/api/${this.projectId}/categories/${categoryId}/tickets/${ticketId}`);
        this.fetchProject();
      } catch (error) {
        console.error('Error deleting ticket:', error);
      }
    }
  }
};
</script>

<style scoped>
    #btnCategorie{
        margin-top:10px !important;
    }
</style>