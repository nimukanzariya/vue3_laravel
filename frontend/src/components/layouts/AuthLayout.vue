<template>
  <div>
    <section class="body-sign">
      <router-view />
    </section>
  </div>

</template>
<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.1s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
<script>
export default {
};
</script>
