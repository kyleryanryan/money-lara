<script setup>
const props = defineProps({
  searchQuery: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(['update:searchQuery']);

const handleInput = (event) => {
  emit('update:searchQuery', event.target.value);
};
</script>

<template>
  <div class="mb-4">
    <input
      :value="searchQuery"
      @input="handleInput"
      type="text"
      placeholder="Search posts..."
      class="form-control"
    />
  </div>
</template>
