            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-auto">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto py-4">
                        <span class="text-gray-600">Copyright &copy; Laundry Management System <?php echo date('Y'); ?></span>
                        <div class="mt-2 small text-gray-500">
                            <span>Made with <i class="fas fa-heart text-danger"></i> for a better laundry business</span>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
    </div>
    
    <!-- Toast Container for Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-bell me-2 text-primary"></i>
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Operation completed successfully.
            </div>
        </div>
    </div>
    
    <!-- jQuery harus dimuat terlebih dahulu -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <script src="assets/js/animations.js"></script>
    
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            
            // Save preference in localStorage
            if (document.body.classList.contains('sb-sidenav-toggled')) {
                localStorage.setItem('sidebarToggled', 'true');
            } else {
                localStorage.setItem('sidebarToggled', 'false');
            }
        });
        
        // Check for saved preference on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebarToggled') === 'true') {
                document.body.classList.add('sb-sidenav-toggled');
            }
            
            // Enable tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('liveToast');
            const toastBody = toast.querySelector('.toast-body');
            const toastHeader = toast.querySelector('.toast-header');
            
            // Set icon and color based on type
            const icon = toast.querySelector('.toast-header i');
            icon.className = 'fas me-2 ';
            
            switch(type) {
                case 'success':
                    icon.className += 'fa-check-circle text-success';
                    break;
                case 'danger':
                    icon.className += 'fa-exclamation-circle text-danger';
                    break;
                case 'warning':
                    icon.className += 'fa-exclamation-triangle text-warning';
                    break;
                case 'info':
                    icon.className += 'fa-info-circle text-info';
                    break;
                default:
                    icon.className += 'fa-bell text-primary';
            }
            
            // Set message
            toastBody.textContent = message;
            
            // Show toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }
    </script>
</body>
</html>
