@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Categories for {{ $owner->username }}</h2>
    <button class="btn btn-primary mb-3" onclick="addCategory()">Add Category</button>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="category-list">
        </tbody>
    </table>
</div>

<script>
    async function loadCategories() {
        const response = await axios.get(`/api/owners/{{ $owner->id }}/categories`);
        const categories = response.data;
        let html = '';
        categories.forEach(category => {
            html += `<tr>
                        <td>${category.name}</td>
                        <td>${category.slug}</td>
                        <td>
                            <button class="btn btn-warning btn-sm">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id})">Delete</button>
                        </td>
                     </tr>`;
        });
        document.getElementById('category-list').innerHTML = html;
    }

    function addCategory() {
        Swal.fire({
            title: 'Add Category',
            input: 'text',
            inputPlaceholder: 'Enter category name',
            showCancelButton: true,
            confirmButtonText: 'Save',
            preConfirm: (name) => {
                axios.post(`/api/owners/{{ $owner->id }}/categories`, { name }).then(() => {
                    Swal.fire('Success', 'Category added!', 'success');
                    loadCategories();
                }).catch(() => {
                    Swal.fire('Error', 'Failed to add category', 'error');
                });
            }
        });
    }

    async function deleteCategory(id) {
        await axios.delete(`/api/owners/{{ $owner->id }}/categories/${id}`);
        loadCategories();
    }

    loadCategories();
</script>
@endsection
