<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="d-flex min-vh-100 flex-column align-items-center justify-content-center bg-light">
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 text-muted" />
            </Link>
        </div>

        <div class="mt-4 w-100 bg-white shadow p-4 rounded" style="max-width: 400px;">
            <slot />
        </div>
    </div>
</template>
