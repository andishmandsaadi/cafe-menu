<template>
  <div class="container mt-4">
    <h2>Products</h2>

    <!-- Add New Product Button -->
    <button class="btn btn-primary mb-3" @click="showModal = true">Add New Product</button>

    <!-- Product Table -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Price</th>
          <th>Categories</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(product, index) in products" :key="product.id">
          <td>{{ index + 1 }}</td>
          <td>
            <img
              v-if="product.image"
              :src="`/storage/${product.image}`"
              alt="Product Image"
              style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
            />
          </td>
          <td>{{ product.name }}</td>
          <td>{{ product.price }}</td>
          <td>
            <span v-for="category in product.categories" :key="category.id" class="badge bg-secondary me-1">
              {{ category.name }}
            </span>
          </td>
          <td>
            <button class="btn btn-warning btn-sm" @click="editProduct(product)">Edit</button>
            <button class="btn btn-danger btn-sm" @click="deleteProduct(product.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Add/Edit Product Modal -->
    <div v-if="showModal" class="modal d-block bg-secondary bg-opacity-50">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Product' : 'Add Product' }}</h5>
            <button class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="name" class="form-label">Product Name</label>
              <input type="text" id="name" v-model="productForm.name" class="form-control" placeholder="Enter product name">
            </div>
            <div class="mb-3">
              <label for="price" class="form-label">Price</label>
              <input type="number" id="price" v-model="productForm.price" class="form-control" placeholder="Enter price">
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea id="description" v-model="productForm.description" class="form-control" placeholder="Enter description"></textarea>
            </div>
            <div class="mb-3">
              <label for="image" class="form-label">Image</label>
              <input type="file" id="image" @change="handleImageUpload" class="form-control">
            </div>
            <div class="mb-3">
              <label for="categories" class="form-label">Categories</label>
              <select id="categories" v-model="productForm.categories" multiple class="form-control">
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button class="btn btn-primary" @click="saveProduct">{{ isEditing ? 'Update' : 'Save' }}</button>
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
      products: [],
      categories: [],
      showModal: false,
      isEditing: false,
      productForm: {
        id: '',
        name: '',
        price: '',
        description: '',
        image: null,
        categories: [],
      },
    };
  },
  mounted() {
    this.fetchProducts();
    this.fetchCategories();
  },
  methods: {
    // Fetch all products
    async fetchProducts() {
      try {
        const response = await axios.get('/api/products');
        this.products = response.data;
      } catch (error) {
        console.error('Error fetching products:', error);
      }
    },

    // Fetch all categories
    async fetchCategories() {
      try {
        const response = await axios.get('/api/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    // Show edit modal with product data
    editProduct(product) {
      this.productForm = {
        id: product.id,
        name: product.name,
        price: product.price,
        description: product.description,
        image: null,
        categories: product.categories.map((category) => category.id),
      };
      this.isEditing = true;
      this.showModal = true;
    },

    // Save or update product
    async saveProduct() {
      this.isSaving = true;
      try {
        const formData = new FormData();
        formData.append('name', this.productForm.name);
        formData.append('price', this.productForm.price);
        formData.append('description', this.productForm.description);
        formData.append('categories', JSON.stringify(this.productForm.categories));
        if (this.productForm.image) {
          formData.append('image', this.productForm.image);
        }

        if (this.isEditing) {
          await axios.post(`/api/products/${this.productForm.id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
          Swal.fire('Updated!', 'Product updated successfully.', 'success');
        } else {
          await axios.post('/api/products', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
          Swal.fire('Added!', 'Product added successfully.', 'success');
        }
        this.closeModal();
        this.fetchProducts();
      } catch (error) {
        Swal.fire('Error!', 'Something went wrong.', 'error');
        console.error('Error saving product:', error);
      } finally {
        this.isSaving = false;
      }
    },

    // Handle image upload
    handleImageUpload(event) {
      this.productForm.image = event.target.files[0];
    },

    // Delete product
    async deleteProduct(id) {
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
            await axios.delete(`/api/products/${id}`);
            Swal.fire('Deleted!', 'Product has been deleted.', 'success');
            this.fetchProducts();
          } catch (error) {
            Swal.fire('Error!', 'Could not delete product.', 'error');
          }
        }
      });
    },

    // Close the modal
    closeModal() {
      this.showModal = false;
      this.isEditing = false;
      this.productForm = {
        id: '',
        name: '',
        price: '',
        description: '',
        image: null,
        categories: [],
      };
    },
  },
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