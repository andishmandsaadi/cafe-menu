@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Cafe Owners</h2>
    <button class="btn btn-primary mb-3" onclick="showAddOwnerModal()">Add New Owner</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($owners as $owner)
            <tr>
                <td>{{ $owner->first_name }} {{ $owner->last_name }}</td>
                <td>{{ $owner->username }}</td>
                <td>{{ $owner->email }}</td>
                <td>
                    <a href="/owners/{{ $owner->id }}" class="btn btn-info btn-sm">Manage Categories</a>
                    <button class="btn btn-warning btn-sm" onclick="editOwner({{ $owner->id }})">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteOwner({{ $owner->id }})">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showAddOwnerModal() {
        Swal.fire({
            title: 'Add Owner',
            html: `<input type="text" id="first_name" class="swal2-input" placeholder="First Name">
                   <input type="text" id="last_name" class="swal2-input" placeholder="Last Name">
                   <input type="email" id="email" class="swal2-input" placeholder="Email">
                   <input type="text" id="username" class="swal2-input" placeholder="Username">`,
            confirmButtonText: 'Save',
            preConfirm: () => {
                axios.post('/api/owners', {
                    first_name: document.getElementById('first_name').value,
                    last_name: document.getElementById('last_name').value,
                    email: document.getElementById('email').value,
                    username: document.getElementById('username').value,
                }).then(response => {
                    Swal.fire('Success', 'Owner added successfully', 'success').then(() => {
                        location.reload();
                    });
                }).catch(error => {
                    Swal.fire('Error', 'Failed to add owner', 'error');
                });
            }
        });
    }

    function deleteOwner(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/api/owners/${id}`).then(() => {
                    Swal.fire('Deleted!', 'Owner has been deleted.', 'success').then(() => {
                        location.reload();
                    });
                }).catch(() => {
                    Swal.fire('Error!', 'Failed to delete owner.', 'error');
                });
            }
        });
    }
</script>
@endsection
