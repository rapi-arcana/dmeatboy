<script setup>
import { reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  artikel: Array,
});
const form = reactive({
  title: props.artikel?.title || '',
  slug: props.artikel?.slug || '',
  excerpt: props.artikel?.excerpt || '',
  image: props.artikel?.image || '',
  date: props.artikel?.date || '',
});


watch(() => form.title, (newTitle) => {
  form.slug = newTitle
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)+/g, '');
});

function submit() {
  router.put(route('dashboard.artikel.update', props.artikel.slug), { ...form });
}

</script>

<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-[#1a1b1e] p-6">
      <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-secondary">
           Edit Artikel
          </h1>
          <p class="text-gray-400 mt-2">
           Perbarui informasi artikel.
          </p>
        </div>

        <!-- Form Card -->
        <div class="bg-[#2c2e33] rounded-xl shadow-lg p-6">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Title Input -->
            <div>
              <label class="block text-secondary text-sm font-medium mb-2">
                Judul
              </label>
              <input 
                v-model="form.title"
                type="text"
                class="w-full bg-[#1a1b1e] border border-[#3d3f45] rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                       transition-colors"
                placeholder="Masukkan judul artikel"
              >
            </div>

            <!-- Slug Input -->
            <div>
              <label class="block text-secondary text-sm font-medium mb-2">
                Slug
              </label>
              <input 
                v-model="form.slug"
                type="text"
                class="w-full bg-[#1a1b1e] border border-[#3d3f45] rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                       transition-colors"
                placeholder="artikel-slug"
              >
            </div>

            <!-- Excerpt Textarea -->
            <div>
              <label class="block text-secondary text-sm font-medium mb-2">
                Excerpt
              </label>
              <textarea 
                v-model="form.excerpt"
                rows="4"
                class="w-full bg-[#1a1b1e] border border-[#3d3f45] rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                       transition-colors resize-none"
                placeholder="Tuliskan ringkasan artikel"
              ></textarea>
            </div>

            <!-- Image URL Input -->
            <div>
              <label class="block text-secondary text-sm font-medium mb-2">
                URL Gambar
              </label>
              <input 
                v-model="form.image"
                type="text"
                class="w-full bg-[#1a1b1e] border border-[#3d3f45] rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                       transition-colors"
                placeholder="https://example.com/image.jpg"
              >
            </div>

            <!-- Date Input -->
            <div>
              <label class="block text-secondary text-sm font-medium mb-2">
                Tanggal
              </label>
              <input 
                v-model="form.date"
                type="date"
                class="w-full bg-[#1a1b1e] border border-[#3d3f45] rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500
                       transition-colors [color-scheme:dark]"
              >
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
              <button 
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg
                       transition-colors duration-200 flex items-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Artikel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>


