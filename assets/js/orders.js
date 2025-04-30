/**
 * Orders management JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for orders
    const ordersTable = $('#ordersTable').DataTable({
        ajax: {
            url: "ajax/get_orders.php",
            dataSrc: ""
        },
        columns: [
            { data: "row_number" },
            { data: "customer_name" },
            { data: "service_type" },
            { 
                data: "weight",
                render: function(data, type, row) {
                    // Periksa jika ada data di order_services
                    if (row.order_services && row.order_services.length > 0) {
                        // Tampilkan multiple services
                        return row.order_services.map(service => {
                            if (service.weight > 0) {
                                return service.weight + ' kg';
                            } else {
                                return service.quantity + ' item';
                            }
                        }).join('<br>');
                    }
                    // Default jika tidak ada data spesifik
                    return data + ' kg';
                }
            },
            { 
                data: "total_amount",
                render: function(data) {
                    return formatCurrency(data);
                }
            },
            { 
                data: "status",
                render: function(data) {
                    return getStatusBadge(data);
                }
            },
            { 
                data: "payment_status",
                render: function(data) {
                    let badge = '';
                    if (!data) data = 'Belum Dibayar';
                    
                    if (data === 'Lunas') {
                        badge = '<span class="badge bg-success">Lunas</span>';
                    } else {
                        badge = '<span class="badge bg-danger">Belum Dibayar</span>';
                    }
                    return badge;
                }
            },
            { 
                data: "created_at",
                render: function(data) {
                    return formatDate(data);
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    let actions = `
                        <div class="table-actions">
                            <button type="button" class="btn btn-sm btn-primary update-status action-btn" data-id="${row.id}" data-status="${row.status}" data-bs-toggle="tooltip" title="Update Status">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <a href="order_form.php?id=${row.id}" class="btn btn-sm btn-info action-btn" data-bs-toggle="tooltip" title="Edit Order">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="receipt.php?id=${row.id}" class="btn btn-sm btn-success action-btn" data-bs-toggle="tooltip" title="Print Receipt">
                                <i class="fas fa-print"></i>
                            </a>`;

                    // Add payment button for orders that are not fully paid
                    if (row.payment_status !== 'Lunas') {
                        actions += `
                            <button type="button" class="btn btn-sm btn-warning action-btn process-payment" data-id="${row.id}" data-amount="${row.total_amount}" data-bs-toggle="tooltip" title="Process Payment">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>`;
                    }

                    // Add delete button (admin only will be able to see it due to server-side check)
                    actions += `
                            <a href="javascript:void(0);" onclick="confirmDeleteOrder(${row.id})" class="btn btn-sm btn-danger action-btn delete-btn" data-bs-toggle="tooltip" title="Delete Order">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    `;
                    
                    return actions;
                }
            }
        ],
        order: [[0, 'desc']],
        responsive: true,
        pageLength: 10,
        language: {
            emptyTable: "Tidak ada data pesanan ditemukan"
        },
        drawCallback: function() {
            // Add animation to table rows after data is loaded
            animateTableRows();
            
            // Initialize tooltips and other interactions
            addAnimationListeners();
            
            // Reinitialize payment buttons after data is reloaded
            initializePaymentButtons();
        }
    });

    // Handle order search
    $('#searchButton').on('click', function() {
        const searchValue = $('#orderSearch').val();
        ordersTable.search(searchValue).draw();
    });

    $('#orderSearch').on('keyup', function(e) {
        if (e.key === 'Enter') {
            ordersTable.search(this.value).draw();
        }
    });

    // Handle status update modal
    $(document).on('click', '.update-status', function() {
        const orderId = $(this).data('id');
        const currentStatus = $(this).data('status');
        
        $('#order_id').val(orderId);
        $('#status').val(currentStatus);
        
        // Show modal
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        statusModal.show();
    });

    // Handle status update form submission
    $('#saveStatus').on('click', function() {
        const orderId = $('#order_id').val();
        const newStatus = $('#status').val();
        
        // Show loading indicator
        $(this).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        $(this).prop('disabled', true);
        
        // Update order status via AJAX
        $.ajax({
            url: 'ajax/update_order_status.php',
            type: 'POST',
            data: {
                order_id: orderId,
                status: newStatus
            },
            dataType: 'json',
            success: function(response) {
                // Hide modal
                const statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
                statusModal.hide();
                
                if (response.success) {
                    showToast('Status pesanan berhasil diperbarui', 'success');
                    // Reload DataTable
                    $('#ordersTable').DataTable().ajax.reload();
                } else {
                    showToast(response.message || 'Gagal memperbarui status pesanan', 'danger');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan saat menghubungi server', 'danger');
            },
            complete: function() {
                // Reset button
                $('#saveStatus').html('Simpan Perubahan');
                $('#saveStatus').prop('disabled', false);
            }
        });
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Function to initialize payment buttons
    function initializePaymentButtons() {
        // Payment button handler
        $('.process-payment').off('click').on('click', function() {
            const orderId = $(this).data('id');
            const orderAmount = $(this).data('amount');
            
            $('#payment_order_id').val(orderId);
            $('#payment_total').text(formatCurrency(orderAmount));
            $('#payment_amount').val('');
            $('#payment_change_display').text('Rp 0');
            $('#payment_method').val('Cash'); // Default to Cash
            
            // Clear previous calculations
            $('#payment_amount').data('order-amount', orderAmount);
            
            // Show modal
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModal.show();
            
            // Focus on payment amount field
            setTimeout(() => {
                $('#payment_amount').focus();
            }, 500);
        });
    }
    
    // Initial setup of payment buttons
    initializePaymentButtons();
});

// Function to confirm delete order
function confirmDeleteOrder(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.')) {
        window.location.href = `orders.php?action=delete&id=${id}`;
    }
}

// Calculate change when payment amount is entered
$(document).on('input', '#payment_amount', function() {
    const orderAmount = parseFloat($(this).data('order-amount'));
    const paymentAmount = parseFloat($(this).val()) || 0;
    
    // Calculate change (kembalian)
    let change = paymentAmount - orderAmount;
    if (change < 0) change = 0;
    
    // Update change display
    $('#payment_change_display').text(formatCurrency(change));
    
    // Set the hidden field value
    $('#payment_change').val(change);
    
    // Visual feedback on the payment amount
    if (paymentAmount < orderAmount) {
        $(this).addClass('is-invalid').removeClass('is-valid');
        $('#payment_amount_feedback').text('Jumlah pembayaran kurang dari total tagihan');
        $('#processPayment').prop('disabled', true);
    } else {
        $(this).addClass('is-valid').removeClass('is-invalid');
        $('#payment_amount_feedback').text('');
        $('#processPayment').prop('disabled', false);
    }
    
    // Automatic animation when calculating change
    $('.cash-only').addClass('animate__animated animate__pulse');
    setTimeout(function() {
        $('.cash-only').removeClass('animate__animated animate__pulse');
    }, 500);
});

// Handle payment method change
$(document).on('change', '#payment_method', function() {
    const method = $(this).val();
    
    if (method === 'Cash') {
        $('.cash-only').show();
    } else {
        // For non-cash payments, just require the exact amount
        $('.cash-only').hide();
        const orderAmount = parseFloat($('#payment_amount').data('order-amount'));
        $('#payment_amount').val(orderAmount);
        $('#payment_change_display').text('Rp 0');
        $('#payment_change').val(0);
        $('#payment_amount').addClass('is-valid').removeClass('is-invalid');
        $('#processPayment').prop('disabled', false);
    }
});
