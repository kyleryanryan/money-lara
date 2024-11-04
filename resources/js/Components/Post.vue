<script setup>
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

dayjs.extend(relativeTime);

const props = defineProps(['post']);

const { props: pageProps } = usePage();

const isOwner = computed(() => pageProps.auth.user?.id === props.post.user.id);

const form = useForm({
    body: props.post.body,
});

const editing = ref(false);

const pluralize = (count, noun, suffix = 's') =>
  `${count} ${noun}${count !== 1 ? suffix : ''}`;

</script>

<template>
    <div class="d-flex p-4 border-bottom">
        <!-- Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="me-3" style="width: 40px; height: 40px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>

        <div class="flex-grow-1">
            <!-- User and Post Info -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-dark">{{ isOwner ? 'Your post' : post.user.name }}</strong>
                    <small class="text-muted ms-2">{{ dayjs(post.created_at).fromNow() }}</small>
                    <small v-if="post.created_at !== post.updated_at" class="text-muted"> &middot; edited</small>
                </div>
            </div>

            <!-- Post Title -->
            <p class="mt-3 fw-bold">{{ post.title }}</p> 

            <!-- Post Body -->
            <p class="mt-2">{{ post.body }}</p> 
        </div>
    </div>
</template>
