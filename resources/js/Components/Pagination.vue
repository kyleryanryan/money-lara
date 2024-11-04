<script setup>
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    posts: {
        type: Object,
        required: true,
    },
    searchQuery: {
        type: String,
        required: false, // Optional if no search is present
    },
});

const goToPage = (page) => {
    router.get(route('posts'), { page, search: props.searchQuery || '' }, { preserveState: true });
};
</script>

<template>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <!-- Previous Link -->
            <li :class="['page-item', !posts.prev_page_url && 'disabled']">
                <Link :href="posts.prev_page_url" @click.prevent="goToPage(posts.current_page - 1)" class="page-link">
                    Previous
                </Link>
            </li>

            <!-- Page Numbers -->
            <li v-for="page in posts.last_page" :key="page" class="page-item">
                <Link
                    :href="posts.path"
                    @click.prevent="goToPage(page)"
                    :class="['page-link', posts.current_page === page ? 'active' : '']"
                >
                    {{ page }}
                </Link>
            </li>

            <!-- Next Link -->
            <li :class="['page-item', !posts.next_page_url && 'disabled']">
                <Link :href="posts.next_page_url" @click.prevent="goToPage(posts.current_page + 1)" class="page-link">
                    Next
                </Link>
            </li>
        </ul>
    </nav>
</template>
