import { defineStore } from 'pinia';

export const useMainStore = defineStore('main', {
    state: () => ({
        categories: [],
        products: [],
    }),
    actions: {
        async fetchCategories() {
            const response = await axios.get('/api/categories');
            this.categories = response.data;
        }
    }
});
