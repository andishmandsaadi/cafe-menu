<template>
  <div class="container mt-4">
    <h2>Categories</h2>

    <!-- Add New Category Button -->
    <button class="btn btn-primary mb-3" @click="showModal = true">Add New Category</button>

    <!-- Category Table -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(category, index) in categories" :key="category.id">
          <td>{{ index + 1 }}</td>
          <td>{{ category.name }}</td>
          <td>{{ category.slug }}</td>
          <td>
            <button class="btn btn-warning btn-sm" @click="editCategory(category)">Edit</button>
            <button class="btn btn-danger btn-sm" @click="deleteCategory(category.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Add/Edit Category Modal -->
    <div v-if="showModal" class="modal d-block bg-secondary bg-opacity-50">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Category' : 'Add Category' }}</h5>
            <button class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="name" class="form-label">Category Name</label>
              <input type="text" id="name" v-model="categoryForm.name" class="form-control" placeholder="Enter category name">
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button class="btn btn-primary" @click="saveCategory">{{ isEditing ? 'Update' : 'Save' }}</button>
          </div>
        </div>
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
      categories: [],
      showModal: false,
      isEditing: false,
      categoryForm: {
        id: '',
        name: ''
      }
    };
  },
  mounted() {
    this.fetchCategories();
  },
  methods: {
    // Fetch all categories
    async fetchCategories() {
      try {
        const response = await axios.get('/api/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    // Show edit modal with category data
    editCategory(category) {
      this.categoryForm = { ...category };
      this.isEditing = true;
      this.showModal = true;
    },

    // Save or update category
    async saveCategory() {
      try {
        if (this.isEditing) {
          await axios.put(`/api/categories/${this.categoryForm.id}`, this.categoryForm);
          Swal.fire('Updated!', 'Category updated successfully.', 'success');
        } else {
          await axios.post('/api/categories', this.categoryForm);
          Swal.fire('Added!', 'Category added successfully.', 'success');
        }
        this.closeModal();
        this.fetchCategories();
      } catch (error) {
        Swal.fire('Error!', 'Something went wrong.', 'error');
        console.error('Error saving category:', error);
      }
    },

    // Delete category
    async deleteCategory(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/api/categories/${id}`);
            Swal.fire('Deleted!', 'Category has been deleted.', 'success');
            this.fetchCategories();
          } catch (error) {
            Swal.fire('Error!', 'Could not delete category.', 'error');
          }
        }
      });
    },

    // Close the modal
    closeModal() {
      this.showModal = false;
      this.isEditing = false;
      this.categoryForm = { id: '', name: '' };
    }
  }
};
</script>

<style>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
