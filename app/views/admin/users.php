<?php require_once __DIR__ . '/../inc/admin_header.php'; ?>

<main class="admin-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mt-4">
                <h1><?php echo $data['title']; ?></h1>
                <p class="lead"><?php echo $data['description']; ?></p>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php 
        $success = Session::flash('success');
        $error = Session::flash('error');
        if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-funnel"></i> Filter
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo URLROOT; ?>/admin/users" class="row g-3">
                            <div class="col-md-4">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-select">
                                    <option value="">-- All Roles --</option>
                                    <?php foreach ($data['roles'] as $role): ?>
                                        <option value="<?php echo $role; ?>" 
                                                <?php echo $data['roleFilter'] == $role ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($role); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Apply Filter
                                </button>
                                <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear Filter
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Users List 
                            <span class="badge bg-secondary"><?php echo $data['totalRecords']; ?> Total</span>
                        </h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-plus-circle"></i> Add User
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th class="text-nowrap text-center" style="width: 200px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['users'])): ?>
                                        <?php $counter = ($data['currentPage'] - 1) * 25 + 1; ?>
                                        <?php foreach ($data['users'] as $user): ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td><?php echo htmlspecialchars($user->name); ?></td>
                                                <td><?php echo htmlspecialchars($user->email); ?></td>
                                                <td>
                                                    <?php 
                                                        $badgeClass = '';
                                                        switch($user->role) {
                                                            case 'administrator':
                                                                $badgeClass = 'bg-danger';
                                                                break;
                                                            case 'editor':
                                                                $badgeClass = 'bg-primary';
                                                                break;
                                                            case 'reviewer':
                                                                $badgeClass = 'bg-info';
                                                                break;
                                                            case 'viewer':
                                                                $badgeClass = 'bg-secondary';
                                                                break;
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo ucfirst(htmlspecialchars($user->role)); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($user->created_at)); ?></td>
                                                <td class="text-nowrap">
                                                    <div class="d-flex gap-2 align-items-center justify-content-center">
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                                onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Edit this user" aria-label="Edit user">
                                                            <i class="bi bi-pencil-square me-1"></i>
                                                            <span class="d-none d-sm-inline">Edit</span>
                                                        </button>
                                                        <?php if ($user->id != Session::get('user_id')): ?>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="deleteUser(<?php echo $user->id; ?>, '<?php echo htmlspecialchars($user->name); ?>')"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="Delete this user" aria-label="Delete user">
                                                                <i class="bi bi-trash me-1"></i>
                                                                <span class="d-none d-sm-inline">Delete</span>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-sm btn-secondary" disabled
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="You cannot delete your own account" aria-label="Delete disabled">
                                                                <i class="bi bi-slash-circle me-1"></i>
                                                                <span class="d-none d-sm-inline">Delete</span>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No users found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($data['totalPages'] > 1): ?>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing page <?php echo $data['currentPage']; ?> of <?php echo $data['totalPages']; ?>
                                    (<?php echo $data['totalRecords']; ?> total records)
                                </div>
                                <nav aria-label="Users pagination">
                                    <ul class="pagination mb-0">
                                        <!-- Previous Button -->
                                        <li class="page-item <?php echo $data['currentPage'] <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $data['currentPage'] > 1 ? URLROOT . '/admin/users?page=' . ($data['currentPage'] - 1) . ($data['roleFilter'] ? '&role=' . urlencode($data['roleFilter']) : '') : '#'; ?>">
                                                Previous
                                            </a>
                                        </li>

                                        <?php
                                        $startPage = max(1, $data['currentPage'] - 2);
                                        $endPage = min($data['totalPages'], $data['currentPage'] + 2);
                                        
                                        if ($startPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?php echo URLROOT . '/admin/users?page=1' . ($data['roleFilter'] ? '&role=' . urlencode($data['roleFilter']) : ''); ?>">1</a>
                                            </li>
                                            <?php if ($startPage > 2): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?php echo $i == $data['currentPage'] ? 'active' : ''; ?>">
                                                <a class="page-link" href="<?php echo URLROOT . '/admin/users?page=' . $i . ($data['roleFilter'] ? '&role=' . urlencode($data['roleFilter']) : ''); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($endPage < $data['totalPages']): ?>
                                            <?php if ($endPage < $data['totalPages'] - 1): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?php echo URLROOT . '/admin/users?page=' . $data['totalPages'] . ($data['roleFilter'] ? '&role=' . urlencode($data['roleFilter']) : ''); ?>">
                                                    <?php echo $data['totalPages']; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Next Button -->
                                        <li class="page-item <?php echo $data['currentPage'] >= $data['totalPages'] ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo $data['currentPage'] < $data['totalPages'] ? URLROOT . '/admin/users?page=' . ($data['currentPage'] + 1) . ($data['roleFilter'] ? '&role=' . urlencode($data['roleFilter']) : '') : '#'; ?>">
                                                Next
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo URLROOT; ?>/admin/users">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label for="add_name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="add_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="add_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="add_password" name="password" required minlength="6">
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_role" class="form-label">Role *</label>
                        <select class="form-select" id="add_role" name="role" required>
                            <option value="">-- Select Role --</option>
                            <option value="administrator">Administrator</option>
                            <option value="editor">Editor</option>
                            <option value="reviewer">Reviewer</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo URLROOT; ?>/admin/users">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password" minlength="6">
                        <small class="text-muted">Leave blank to keep current password</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role *</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="administrator">Administrator</option>
                            <option value="editor">Editor</option>
                            <option value="reviewer">Reviewer</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo URLROOT; ?>/admin/users">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <p>Are you sure you want to delete <strong id="delete_name"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_password').value = '';
    
    var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
}

function deleteUser(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    deleteModal.show();
}

// Initialize Bootstrap tooltips for action buttons
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
    new bootstrap.Tooltip(el);
});
</script>

<?php require_once __DIR__ . '/../inc/admin_footer.php'; ?>