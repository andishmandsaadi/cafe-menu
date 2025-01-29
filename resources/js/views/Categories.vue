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
          <th>Image</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(category, index) in categories" :key="category.id">
          <td>{{ index + 1 }}</td>
          <td>
            <img v-if="category.image" :src="`/storage/${category.image}`" alt="Category Image" class="img-thumbnail" style="max-width: 100px;">
            <span v-else>No Image</span>
          </td>
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
            <div class="mb-3">
              <label for="image" class="form-label">Category Image</label>
              <input type="file" id="image" @change="handleImageUpload" class="form-control">
              <img v-if="categoryForm.imagePreview" :src="categoryForm.imagePreview" alt="Image Preview" class="img-thumbnail mt-2" style="max-width: 100px;">
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
        name: '',
        image: null,
        imagePreview: ''
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
      this.categoryForm = {
        ...category,
        imagePreview: category.image ? `/storage/${category.image}` : ''
      };
      this.isEditing = true;
      this.showModal = true;
    },

    // Handle image upload
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.categoryForm.image = file;
        this.categoryForm.imagePreview = URL.createObjectURL(file);
      }
    },

    // Save or update category
    async saveCategory() {
      try {
        const formData = new FormData();
        formData.append('name', this.categoryForm.name);
        if (this.categoryForm.image) {
          formData.append('image', this.categoryForm.image);
        }

        if (this.isEditing) {
          await axios.post(`/api/categories/${this.categoryForm.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
          Swal.fire('Updated!', 'Category updated successfully.', 'success');
        } else {
          await axios.post('/api/categories', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
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
      this.categoryForm = { id: '', name: '', image: null, imagePreview: '' };
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

.img-thumbnail {
  max-width: 100px;
  height: auto;
}
</style>
