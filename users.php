<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isAdmin()) {
    setFlashMessage('danger', 'You do not have permission to access that page.');
    header('Location: dashboard.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $currentUserId = $_SESSION['user_id'];

    if ($id == $currentUserId) {
        setFlashMessage('danger', 'You cannot delete your own account.');
    } else {
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            setFlashMessage('success', 'User deleted successfully.');
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Error deleting user: ' . $e->getMessage());
        }
    }

    header('Location: users.php');
    exit;
}

$pageTitle = "User Management";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Management</h1>
        <a href="user_form.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add New User
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">All Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filled by AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = $('#usersTable').DataTable({
        ajax: {
            url: 'ajax/get_users.php',
            dataSrc: ''
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'username' },
            { 
                data: 'role',
                render: function(data) {
                    let badge = '';
                    switch(data) {
                        case 'admin':
                            badge = '<span class="badge bg-primary">Admin</span>';
                            break;
                        case 'cashier':
                            badge = '<span class="badge bg-info">Cashier</span>';
                            break;
                        default:
                            badge = '<span class="badge bg-secondary">' + data + '</span>';
                    }
                    return badge;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return formatDate(data);
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    const currentUserId = <?php echo $_SESSION['user_id']; ?>;
                    const isCurrentUser = data.id == currentUserId;

                    let editButton = `
                        <a href="user_form.php?id=${data.id}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    `;

                    let deleteButton = '';
                    if (!isCurrentUser) {
                        deleteButton = `
                            <a href="javascript:void(0);" onclick="confirmDelete(${data.id})" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        `;
                    }

                    return `
                        <div class="table-actions">
                            ${editButton}
                            ${deleteButton}
                        </div>
                    `;
                }
            }
        ],
        order: [[1, 'asc']]
    });

    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        window.location.href = `users.php?action=delete&id=${id}`;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
