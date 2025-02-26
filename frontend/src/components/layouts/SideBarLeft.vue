<template>
  <!-- start: sidebar -->
  <aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
      <div class="sidebar-title">
        Navigation
      </div>
      <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html"
        data-fire-event="sidebar-left-toggle">
        <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
      </div>
    </div>
    <div class="nano">
      <div class="nano-content">
        <nav id="menu" class="nav-main" role="navigation">
          <ul class="nav nav-main">
            <li>
              <a href="/" class="nav-link">
                <i class="bx bx-home-alt" aria-hidden="true"></i>
                <span>Stock</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link" @click="logout">
                <i class="bx bx-home-alt" aria-hidden="true"></i>
                <span>Log out</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </aside>
  <!-- end: sidebar -->
</template>
<script>
import axios from 'axios';

export default {
  methods: {
    async logout() {
      const token = JSON.parse(localStorage.getItem("active-user"))?.access_token;
      await axios.post('http://127.0.0.1:8000/api/logout', {}, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        }
      }).then(response => {
        this.error = {};
        if (response.data.status && response.data.status === "error") {
          this.error = response.data.data;
        } else {
          localStorage.removeItem('active-user');
          this.$router.push('/login');
        }
      }).catch(error => {
        this.error = {};
        this.error.user = error;
      });
    },
  }
};
</script>
<style></style>
