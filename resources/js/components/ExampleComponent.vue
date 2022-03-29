<template>
  <div class="container">
    <div>
      <form>
        <div>
          <label for="email">Email</label>
          <input
            name="email"
            type="text"
            class="form-control"
            v-model="email"
          />
        </div>
        <div>
          <label for="password">Password</label>
          <input
            name="password"
            type="password"
            class="form-control"
            v-model="password"
          />
        </div>
        <br />
        <div class="btn btn-primary" @click="login">Login</div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    console.log("Component mounted.");
  },
  data() {
    return {
      email: null,
      password: null,
    };
  },
  methods: {
    login() {
      axios
        .get("/api/login", {
          params: {
            email: this.email,
            password: this.password,
          },
        })
        .then((res) => {
          window.localStorage.setItem("token", res.data.token);
          const token = window.localStorage.getItem("token");
          if (token == "undefined" || token == null) {
            //window.location.href = "/login-vue";
          } else {
            axios
              .get("/api/check", {
                headers: {
                  Authorization: "Bearer " + token,
                },
              })
              .then((res) => {
                window.location.href = "/check-login";
                console.log(res);
              })
              .catch((err) => {
                console.error(err);
              });
          }
          console.log(res);
        })
        .catch((err) => {
          console.error(err);
        });
    },
  },
};
</script>
