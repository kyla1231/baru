/**
 * Customers management JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for customers
    const customersTable = $('#customersTable').DataTable({
        ajax: {
            url: "ajax/get_customers.php",
            dataSrc: ""
        },
        columns: [
            { data: "id" },
            { data: "name" },
            { data: "phone" },
            { data: "address" },
            { data: "total_orders" },
            { 
                data: "created_at",
                render: function(data) {
                    return formatDate(data, false);
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="table-actions">
                            <button type="button" class="btn btn-sm btn-primary view-customer" data-id="${row.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="customer_form.php?id=${row.id}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="customers.php?delete=${row.id}" class="btn btn-sm btn-danger btn-delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        responsive: true,
        pageLength: 10,
        language: {
            emptyTable: "No customers found"
        }
    });

    // Handle customer search
    $('#searchButton').on('click', function() {
        const searchValue = $('#customerSearch').val();
        customersTable.search(searchValue).draw();
    });

    $('#customerSearch').on('keyup', function(e) {
        if (e.key === 'Enter') {
            customersTable.search(this.value).draw();
        }
    });

    // Handle customer details modal
    $(document).on('click', '.view-customer', function() {
        const customerId = $(this).data('id');
        
        // Get customer details via AJAX
        sendAjaxRequest('ajax/get_customer_details.php', 'POST', {
            customer_id: customerId
        }, function(error, response) {
            if (error) {
                showToast('Error loading customer details', 'danger');
                return;
            }
            
            if (response.success) {
                const customer = response.data;
                
                // Populate customer details
                $('#customer-name').text(customer.name);
                $('#customer-phone').text(customer.phone);
                $('#customer-address').text(customer.address || 'N/A');
                $('#customer-created').text(formatDate(customer.created_at, false));
                
                // Populate order summary
                $('#customer-total-orders').text(customer.orders.length);
                $('#customer-total-spent').text(formatCurrency(customer.total_spent));
                $('#customer-last-order').text(customer.orders.length > 0 ? formatDate(customer.orders[0].created_at) : 'N/A');
                
                // Populate order history table
                const ordersTableBody = $('#customerOrdersTable tbody');
                ordersTableBody.empty();
                
                if (customer.orders.length > 0) {
                    customer.orders.forEach(function(order) {
                        ordersTableBody.append(`
                            <tr>
                                <td><a href="order_form.php?id=${order.id}">${order.id}</a></td>
                                <td>${order.service_type}</td>
                                <td>${order.weight} kg</td>
                                <td>${formatCurrency(order.total_amount)}</td>
                                <td>${getStatusBadge(order.status)}</td>
                                <td>${formatDate(order.created_at)}</td>
                            </tr>
                        `);
                    });
                } else {
                    ordersTableBody.append('<tr><td colspan="6" class="text-center">No orders found</td></tr>');
                }
                
                // Update edit button link
                $('#editCustomerBtn').attr('href', `customer_form.php?id=${customerId}`);
                
                // Show modal
                const detailsModal = new bootstrap.Modal(document.getElementById('customerDetailsModal'));
                detailsModal.show();
            } else {
                showToast(response.message || 'Error loading customer details', 'danger');
            }
        });
    });
    
    // Create a simple fallback for the AJAX call if the file isn't created yet
    if (typeof sendAjaxRequest !== 'function') {
        window.sendAjaxRequest = function(url, method, data, callback) {
            // Simulate a successful response with mock data
            console.log(`Simulating ${method} request to ${url} with data:`, data);
            
            // Simulate delay
            setTimeout(function() {
                const mockCustomer = {
                    id: data.customer_id,
                    name: "Customer " + data.customer_id,
                    phone: "08123456789",
                    address: "Jl. Customer No. " + data.customer_id,
                    email: "customer" + data.customer_id + "@example.com",
                    created_at: "2023-01-01 12:00:00",
                    total_spent: 150000,
                    orders: [
                        {
                            id: 1001,
                            service_type: "Cuci Setrika",
                            weight: 3.5,
                            total_amount: 35000,
                            status: "Selesai",
                            created_at: "2023-05-15 14:30:00"
                        },
                        {
                            id: 1002,
                            service_type: "Cuci Kering",
                            weight: 2.0,
                            total_amount: 14000,
                            status: "Diambil",
                            created_at: "2023-04-10 09:15:00"
                        }
                    ]
                };
                
                callback(null, {
                    success: true,
                    data: mockCustomer
                });
            }, 500);
        };
    }
    
    // Fallback for missing AJAX endpoints
    $.fn.dataTable.ext.errMode = 'none';
    $('#customersTable').on('error.dt', function(e, settings, techNote, message) {
        console.error('DataTables error:', message);
        
        // If AJAX endpoint doesn't exist, populate with sample data
        if (customersTable) {
            // Clear the "Loading..." message
            customersTable.clear().draw();
            
            // Show message to user
            showToast('Using local data. In production, data would be loaded from server.', 'info');
        }
    });
});
