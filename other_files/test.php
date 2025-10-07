<?php
// Example mock data (replace with your DB fetch later)
$car_categories = [
    ['id' => 1, 'name' => 'SUV', 'description' => 'Sport Utility Vehicle'],
    ['id' => 2, 'name' => 'Sedan', 'description' => 'Standard 4-door car'],
    ['id' => 3, 'name' => 'Truck', 'description' => 'Heavy duty vehicle']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Car Categories</h4>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Category Name</th>
                        <th>Description</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($car_categories as $cat): ?>
                        <tr>
                            <td><?= htmlspecialchars($cat['id']) ?></td>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= htmlspecialchars($cat['description']) ?></td>
                            
                        </tr>
                    <?php endforeach; ?>

                    <!-- Add Button Row -->
                    <tr>
                        <td colspan="3" class="text-center">
                            <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                + Add New Category
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="add_category.php">
        <div class="modal-header">
          <h5 class="modal-title" id="addCategoryLabel">Add New Car Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="categoryName" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="categoryName" name="category_name" required placeholder="Enter category name">
            </div>
            <div class="mb-3">
                <label for="categoryDesc" class="form-label">Rental Price/24hr</label>
                <input type="number" class="form-control" id="rental_price" name="rental_price" required placeholder="Enter rental price">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
