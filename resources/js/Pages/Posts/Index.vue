<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import Post from '@/Components/Post.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchBar from '@/Components/SearchBar.vue';
import { ref, watch } from 'vue';

const props = defineProps(['posts', 'search']);

const searchQuery = ref(props.search || '');

watch(() => props.search, (newSearch) => {
  searchQuery.value = newSearch || '';
});

const searchPosts = () => {
  router.get(route('posts'), { search: searchQuery.value || '' }, { preserveState: true });
};
</script>

<template>
  <Head title="Posts" />

  <AuthenticatedLayout>
    <div class="container mt-4">
      <!-- Search Bar -->
      <SearchBar :searchQuery="searchQuery" @update:searchQuery="searchQuery = $event; searchPosts()" />

      <!-- Display posts -->
      <div class="card mb-4" v-for="post in posts.data" :key="post.id">
        <div class="card-body">
          <Post :post="post" />
        </div>
      </div>

      <!-- Pagination -->
      <Pagination :posts="posts" :search-query="searchQuery" />
    </div>
  </AuthenticatedLayout>
</template>
