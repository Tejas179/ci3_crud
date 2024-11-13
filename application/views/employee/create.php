<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Employee Management</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#employeeModal" onclick="resetForm()">Add New Employee</button>
    
    <!-- DataTable -->
    <table id="employeeTable" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Profile Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?= $employee['id']; ?></td>
                <td><?= $employee['first_name']; ?></td>
                <td><?= $employee['last_name']; ?></td>
                <td><?= $employee['mobile']; ?></td>
                <td><?= $employee['address']; ?></td>
                <td><img src="<?= base_url($employee['profile_image']); ?>" alt="Profile Image" width="50"></td>
                <td>
    <!-- Edit Button -->
    <button class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Edit" onclick="editEmployee(<?= $employee['id']; ?>)">
        <i class="mdi mdi-pencil"></i> <!-- MDI edit icon -->
    </button>
    
    <!-- Delete Button -->
    <button class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="confirmDelete(<?= $employee['id']; ?>)">
        <i class="mdi mdi-delete"></i> <!-- MDI delete icon -->
    </button>
</td>


            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal for adding/editing employee -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="employeeForm" action="<?= base_url('employee/store'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Add/Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="employeeId">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="text" class="form-control" name="mobile" id="mobile" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" name="address" id="address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" id="profile_image">
                        <input type="hidden" name="existing_image" id="existing_image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Ensure jQuery is loaded first -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#employeeTable').DataTable();
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    function resetForm() {
        document.getElementById('employeeForm').reset();
        document.getElementById('employeeId').value = '';
        document.getElementById('employeeForm').action = '<?= base_url('employee/store'); ?>';
    }

    function editEmployee(id) {
    $.ajax({
        url: '<?= base_url('Employee/get_employee'); ?>', // No trailing slash needed
        type: 'POST', // POST method for sending data
        data: { id: id }, // Pass the ID as an object
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                Swal.fire('Error', data.error, 'error');
            } else {
                $('#employeeId').val(data.id);
                $('#first_name').val(data.first_name);
                $('#last_name').val(data.last_name);
                $('#mobile').val(data.mobile);
                $('#address').val(data.address);
                $('#existing_image').val(data.profile_image);
                $('#employeeModalLabel').text('Edit Employee');
                $('#employeeForm').attr('action', '<?= base_url('employee/update'); ?>');
                $('#employeeModal').modal('show'); // Show the modal
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Failed to load employee data', 'error');
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
        }
    });
}

    function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url("employee/delete/"); ?>' + id;
        }
    });
}
</script>
<?php if ($this->session->flashdata('success')): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '<?= $this->session->flashdata('success'); ?>'
    });
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= $this->session->flashdata('error'); ?>'
    });
</script>
<?php endif; ?>
</body>
</html>
