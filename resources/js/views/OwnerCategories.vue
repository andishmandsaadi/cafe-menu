<template>
  <div class="container mt-4">
    <h2>Cafe Owner: {{ owner.first_name }} {{ owner.last_name }}</h2>

    <!-- Buttons to open modals -->
    <div class="mb-3">
      <button class="btn btn-primary me-2" @click="showAssignCategoryModal">Assign Category</button>
      <button class="btn btn-success" @click="showAddCategoryModal">Add Category</button>
    </div>

    <!-- Assigned Categories List -->
    <h4>Assigned Categories</h4>
    <ul class="list-group">
      <li class="list-group-item d-flex justify-content-between align-items-center" v-for="category in owner.categories" :key="category.id">
        {{ category.name }}
        <button class="btn btn-danger btn-sm" @click="unassignCategory(category.id)">Unassign</button>
      </li>
    </ul>

    <!-- Assign Category Modal -->
    <div v-if="showAssignModal" class="modal-overlay">
      <div class="modal-content">
        <h4>Assign Categories</h4>
        <div class="form-group">
          <label>Select Categories:</label>
          <select v-model="selectedCategories" multiple class="form-control">
            <option
              v-for="category in unassignedCategories"
              :key="category.id"
              :value="category.id"
              :disabled="isCategoryAssigned(category.id)"
            >
              {{ category.name }}
            </option>
          </select>
        </div>
        <button class="btn btn-primary mt-3" @click="assignCategories">Save</button>
        <button class="btn btn-secondary mt-3 ml-2" @click="showAssignModal = false">Cancel</button>
      </div>
    </div>

    <!-- Add Category Modal -->
    <div v-if="showAddModal" class="modal-overlay">
      <div class="modal-content">
        <h4>Add New Category</h4>
        <div class="form-group">
          <label>Category Name</label>
          <input type="text" v-model="newCategory.name" class="form-control" placeholder="Enter category name" />
        </div>
        <div class="form-group">
          <label>Category Image</label>
          <input type="file" @change="handleImageUpload" class="form-control" />
        </div>
        <button class="btn btn-primary mt-3" @click="addCategory">Save Category</button>
        <button class="btn btn-secondary mt-3 ml-2" @click="showAddModal = false">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      owner: {},
      allCategories: [],
      selectedCategories: [],
      showAssignModal: false,
      showAddModal: false,
      newCategory: {
        name: '',
        image: null,
      },
    };
  },
  computed: {
    // Filter unassigned categories
    unassignedCategories() {
      return this.allCategories.filter(
        (category) => !this.owner.categories.some((assigned) => assigned.id === category.id)
      );
    },
  },
  mounted() {
    this.fetchOwnerData();
  },
  methods: {
    async fetchOwnerData() {
      const ownerId = this.$route.params.id;
      try {
        const response = await axios.get(`/api/cafe-owners/${ownerId}`);
        this.owner = response.data.owner;
        this.allCategories = response.data.categories;
      } catch (error) {
        Swal.fire('Error', 'Failed to load owner data', 'error');
      }
    },
    async assignCategories() {
      const ownerId = this.$route.params.id;
      try {
        await axios.post(`/api/cafe-owners/${ownerId}/assign-categories`, {
          category_ids: this.selectedCategories,
        });
        Swal.fire('Success', 'Categories assigned successfully', 'success');
        this.fetchOwnerData();
        this.showAssignModal = false;
      } catch (error) {
        Swal.fire('Error', error.response.data.message || 'Failed to assign categories', 'error');
      }
    },
    async unassignCategory(categoryId) {
      const ownerId = this.$route.params.id;
      try {
        await axios.delete(`/api/cafe-owners/${ownerId}/unassign-category/${categoryId}`);
        Swal.fire('Success', 'Category unassigned successfully', 'success');
        this.fetchOwnerData();
      } catch (error) {
        Swal.fire('Error', 'Failed to unassign category', 'error');
      }
    },
    async addCategory() {
      if (!this.newCategory.name || !this.newCategory.image) {
        Swal.fire('Warning', 'Please fill in all fields', 'warning');
        return;
      }

      let formData = new FormData();
      formData.append('name', this.newCategory.name);
      formData.append('image', this.newCategory.image);

      try {
        await axios.post('/api/categories', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        Swal.fire('Success', 'Category added successfully', 'success');
        this.showAddModal = false;
        this.fetchOwnerData();
      } catch (error) {
        Swal.fire('Error', 'Failed to add category', 'error');
      }
    },
    handleImageUpload(event) {
      this.newCategory.image = event.target.files[0];
    },
    showAssignCategoryModal() {
      this.showAssignModal = true;
    },
    showAddCategoryModal() {
      this.showAddModal = true;
    },
    isCategoryAssigned(categoryId) {
      return this.owner.categories.some((category) => category.id === categoryId);
    },
  },
};
</script>

<style scoped>
ul.list-group {
  max-width: 400px;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
}

/* Button spacing */
.btn-danger {
  margin-left: auto;
}
</style>