<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Admin Dashboard</h1>

        <div class="list-group">
            <a href="{{ url('/cafe-owners') }}" class="list-group-item list-group-item-action">
                Manage Cafe Owners
            </a>
            <a href="{{ url('/categories') }}" class="list-group-item list-group-item-action">
                Manage Categories
            </a>
            <a href="{{ url('/products') }}" class="list-group-item list-group-item-action">
                Manage Products
            </a>
        </div>
    </div>
</body>
</html>
