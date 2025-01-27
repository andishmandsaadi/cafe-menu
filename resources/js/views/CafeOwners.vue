<template>
  <div class="container mt-4">
    <h2 class="mb-3">Cafe Owners</h2>

    <!-- Add Owner Button -->
    <button class="btn btn-primary mb-3" @click="showAddOwnerModal">Add New Owner</button>

    <!-- Owners Table -->
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Email</th>
          <th>Username</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(owner, index) in owners" :key="owner.id">
          <td>{{ index + 1 }}</td>
          <td>{{ owner.first_name }}</td>
          <td>{{ owner.last_name }}</td>
          <td>{{ owner.email }}</td>
          <td>{{ owner.username }}</td>
          <td>
            <button class="btn btn-warning btn-sm" @click="showEditOwnerModal(owner)">Edit</button>
            <button class="btn btn-danger btn-sm" @click="deleteOwner(owner.id)">Delete</button>
            <router-link :to="'/owners/' + owner.id" class="btn btn-info btn-sm">Manage Categories</router-link>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      owners: [],
    };
  },
  mounted() {
    this.fetchOwners();
  },
  methods: {
    // Fetch all cafe owners from the API
    async fetchOwners() {
      try {
        const response = await axios.get('/api/cafe-owners');
        this.owners = response.data;
      } catch (error) {
        Swal.fire('Error', 'Failed to load cafe owners', 'error');
      }
    },

    // Show Add Owner Modal
    showAddOwnerModal() {
      Swal.fire({
        title: 'Add Cafe Owner',
        html: `
          <input type="text" id="first_name" class="swal2-input" placeholder="First Name">
          <input type="text" id="last_name" class="swal2-input" placeholder="Last Name">
          <input type="email" id="email" class="swal2-input" placeholder="Email">
          <input type="text" id="username" class="swal2-input" placeholder="Username">
          <input type="password" id="password" class="swal2-input" placeholder="Password">
        `,
        confirmButtonText: 'Save',
        preConfirm: () => {
          const firstName = document.getElementById('first_name').value;
          const lastName = document.getElementById('last_name').value;
          const email = document.getElementById('email').value;
          const username = document.getElementById('username').value;
          const password = document.getElementById('password').value;

          if (!firstName || !lastName || !email || !username || !password) {
            Swal.showValidationMessage('All fields are required');
          }
          return { firstName, lastName, email, username, password };
        },
      }).then((result) => {
        if (result.isConfirmed) {
          this.addOwner(result.value);
        }
      });
    },

    // Add a new cafe owner
    async addOwner(ownerData) {
        try {
            await axios.post('/api/cafe-owners', {
            first_name: ownerData.firstName,
            last_name: ownerData.lastName,
            email: ownerData.email,
            username: ownerData.username,
            password: ownerData.password,  // Ensure password is sent
            });

            Swal.fire('Success', 'Cafe owner added successfully', 'success');
            this.fetchOwners();
        } catch (error) {
            if (error.response && error.response.status === 422) {
            const errors = Object.values(error.response.data.errors).flat().join('<br>');
            Swal.fire('Validation Error', errors, 'error');
            } else {
            Swal.fire('Error', error.response.data.message || 'Failed to add cafe owner', 'error');
            }
        }
    },

    // Show Edit Owner Modal
    showEditOwnerModal(owner) {
      Swal.fire({
        title: 'Edit Cafe Owner',
        html: `
          <input type="text" id="first_name" class="swal2-input" value="${owner.first_name}">
          <input type="text" id="last_name" class="swal2-input" value="${owner.last_name}">
          <input type="email" id="email" class="swal2-input" value="${owner.email}">
          <input type="text" id="username" class="swal2-input" value="${owner.username}">
          <input type="password" id="password" class="swal2-input" placeholder="Enter new password (optional)">
        `,
        confirmButtonText: 'Update',
        preConfirm: () => {
          const firstName = document.getElementById('first_name').value;
          const lastName = document.getElementById('last_name').value;
          const email = document.getElementById('email').value;
          const username = document.getElementById('username').value;
          const password = document.getElementById('password').value;

          if (!firstName || !lastName || !email || !username) {
            Swal.showValidationMessage('All fields except password are required');
          }
          return { id: owner.id, firstName, lastName, email, username, password };
        },
      }).then((result) => {
        if (result.isConfirmed) {
          this.updateOwner(result.value);
        }
      });
    },

    // Update an existing cafe owner
    async updateOwner(ownerData) {
      try {
        const payload = {
          first_name: ownerData.firstName,
          last_name: ownerData.lastName,
          email: ownerData.email,
          username: ownerData.username,
        };

        if (ownerData.password) {
          payload.password = ownerData.password;
        }

        await axios.put(`/api/cafe-owners/${ownerData.id}`, payload);
        Swal.fire('Success', 'Cafe owner updated successfully', 'success');
        this.fetchOwners();
      } catch (error) {
        Swal.fire('Error', 'Failed to update cafe owner', 'error');
      }
    },

    // Delete an owner
    async deleteOwner(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/api/cafe-owners/${id}`);
            Swal.fire('Deleted!', 'Cafe owner deleted successfully', 'success');
            this.fetchOwners();
          } catch (error) {
            Swal.fire('Error', 'Failed to delete cafe owner', 'error');
          }
        }
      });
    },
  },
};
</script>

<style scoped>
.table th, .table td {
  vertical-align: middle;
  text-align: center;
}
</style>
