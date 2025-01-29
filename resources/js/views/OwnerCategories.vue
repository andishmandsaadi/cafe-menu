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
        <div>
          <button class="btn btn-info btn-sm me-2" @click="showManageProductsModal(category)">Manage Products</button>
          <button class="btn btn-danger btn-sm" @click="unassignCategory(category.id)">Unassign</button>
        </div>
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

    <!-- Manage Products Modal -->
    <div v-if="showManageProductsModalVisible" class="modal-overlay">
      <div class="modal-content">
        <h4>Manage Products for {{ selectedCategory.name }}</h4>

        <!-- Assign Products Section -->
        <div class="mb-4">
          <h5>Assign Products</h5>
          <div class="form-group">
            <label>Select Products:</label>
            <select v-model="selectedProducts" multiple class="form-control">
              <option
                v-for="product in unassignedProducts"
                :key="product.id"
                :value="product.id"
              >
                {{ product.name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Price for Owner</label>
            <input type="number" v-model="ownerProductPrice" class="form-control" placeholder="Enter price" />
          </div>
          <button class="btn btn-primary" @click="assignProducts">Assign Products</button>
        </div>

        <!-- Add Product Button -->
        <button class="btn btn-success mt-2" @click="showAddProductModal = true">+ Add New Product</button>

        <!-- Assigned Products List -->
        <div>
          <h5>Assigned Products</h5>
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center" v-for="product in assignedProducts" :key="product.id">
              {{ product.name }} - Owner Price: {{ product.owner_price }}
              <button class="btn btn-danger btn-sm" @click="unassignProduct(product.id)">Unassign</button>
            </li>
          </ul>
        </div>

        <button class="btn btn-secondary mt-3" @click="showManageProductsModalVisible = false">Close</button>
      </div>
    </div>

    <!-- Add Product Modal -->
    <div v-if="showAddProductModal" class="modal-overlay">
      <div class="modal-content">
        <h4>Add New Product</h4>
        <div class="form-group">
          <label>Product Name</label>
          <input type="text" v-model="newProduct.name" class="form-control" placeholder="Enter product name" />
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea v-model="newProduct.description" class="form-control" placeholder="Enter product description"></textarea>
        </div>
        <div class="form-group">
          <label>Image</label>
          <input type="file" @change="handleImageUpload" class="form-control" />
        </div>
        <button class="btn btn-primary mt-3" @click="addProduct">Save Product</button>
        <button class="btn btn-secondary mt-3 ml-2" @click="showAddProductModal = false">Cancel</button>
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
      showManageProductsModalVisible: false,
      showAddProductModal: false,
      selectedCategory: {},
      selectedProducts: [],
      ownerProductPrice: null,
      assignedProducts: [],
      unassignedProducts: [],
      newProduct: {
        name: '',
        description: '',
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
    async showManageProductsModal(category) {
      this.selectedCategory = category;
      this.showManageProductsModalVisible = true;
      await this.fetchProductsForCategory(category.id);
    },
    async fetchProductsForCategory(categoryId) {
      const ownerId = this.owner.id;
      try {
        const response = await axios.get(`/api/categories/${categoryId}/owner/${ownerId}/products`);
        this.unassignedProducts = response.data.unassignedProducts;
        this.assignedProducts = response.data.assignedProducts;
      } catch (error) {
        Swal.fire('Error', 'Failed to fetch products', 'error');
      }
    },
    async addProduct() {
      if (!this.newProduct.name || !this.newProduct.description) {
        Swal.fire('Warning', 'Please fill in all required fields', 'warning');
        return;
      }

      let formData = new FormData();
      formData.append('name', this.newProduct.name);
      formData.append('description', this.newProduct.description);
        if (this.newProduct.image) {
          formData.append('image', this.newProduct.image);
        }
        formData.append('categories[]', this.selectedCategory.id);

      try {
        const response = await axios.post('/api/products', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        Swal.fire('Success', 'Product added successfully', 'success');
        this.showAddProductModal = false;
        this.fetchProductsForCategory(this.selectedCategory.id); // Refresh product list
      } catch (error) {
        Swal.fire('Error', 'Failed to add product', 'error');
      }
    },
    async assignProducts() {
      if (!this.selectedProducts.length || !this.ownerProductPrice) {
        Swal.fire('Warning', 'Please select products and enter a price', 'warning');
        return;
      }

      const ownerId = this.$route.params.id;
      const categoryId = this.selectedCategory.id;

      try {
        await axios.post(`/api/cafe-owners/${ownerId}/assign-products`, {
          category_id: categoryId,
          product_ids: this.selectedProducts,
          price: this.ownerProductPrice,
        });
        Swal.fire('Success', 'Products assigned successfully', 'success');
        this.fetchProductsForCategory(categoryId);
      } catch (error) {
        Swal.fire('Error', 'Failed to assign products', 'error');
      }
    },
    async unassignProduct(productId) {
      const ownerId = this.$route.params.id;
      const categoryId = this.selectedCategory.id;

      try {
        await axios.delete(`/api/cafe-owners/${ownerId}/unassign-product/${categoryId}/${productId}`);
        Swal.fire('Success', 'Product unassigned successfully', 'success');
        this.fetchProductsForCategory(categoryId);
      } catch (error) {
        Swal.fire('Error', 'Failed to unassign product', 'error');
      }
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