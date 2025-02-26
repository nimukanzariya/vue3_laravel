<template>
    <div>
        <section class="body">
            <TopNavbar />
            <div class="inner-wrapper">
                <SideBarLeft />
                <section role="main" class="content-body">
                    <router-view />
                </section>
            </div>

        </section>
    </div>
</template>
<script>
import SideBarLeft from './SideBarLeft.vue';
import TopNavbar from './TopNavbar.vue';

export default {
    components: {
        TopNavbar,
        SideBarLeft
    }
};
</script>