<template>
  <div class="container">
    <div class="btn btn-success" v-if="check==true">
      Trang này danh cho nhưng người đã đăng nhập
    </div>
  </div>
</template>
<script>
export default {
  mounted() {
    this.checkLoggerIn();
  },
  data() {
    return {
      check: false
    }
  },
  methods: {
    checkLoggerIn() {
      const token = window.localStorage.getItem("token");
      console.log(typeof token);
      if (token == 'undefined' || token == null) {
        window.location.href = "/login-vue";
      } 
      else {
        axios
          .get("/api/check", {
            headers: {
              Authorization: "Bearer " + token,
            },
          })
          .then((res) => {
            if (res == "") {
              window.location.href = "/login-vue";
            }
            else {
              this.check=true;
            }
            console.log(res);
          })
          .catch((err) => {
            console.error(err);
          });
      }
    },
  },
};
</script>
