<template>
  <section>
      <h3 v-show="!!error">{{ error }}</h3>
      <div>
        <label for="email">email:</label>
        <input type="email" id="username" v-model="postData.username">
      </div>
      <div>
        <label for="password">Password: </label>
        <input type="password" id="password" v-model="postData.password">
      </div>
      <button @click="refreshLogin">Connexion</button>

  </section>
</template>

<script>
export default {
  data() {
    return {
      postData: {
        username: '',
        password:  ''
      }
    }
  },
  methods: { 
	refreshLogin() {
	   fetch("/api/login", {
		"method": "POST",
		headers: {
			'Accept': 'application/json',
			'Content-Type': 'application/json;charset=utf-8'
		},
		body: JSON.stringify(this.postData)
		})
           .then( function ( response ) {
		console.log(response);
		if( response.status != 200 ) {
			console.log('invalid');
			this.error = response.status;
		} else {
			console.log('valid');
			response.json().then ( function ( data ){
				this.error = data.token;
			}.bind(this));
		}
		}.bind(this));
	 }
    }
}
</script>
