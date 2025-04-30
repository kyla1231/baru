<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Check if user is admin
if (!isAdmin()) {
    setFlashMessage('danger', 'You do not have permission to access this page.');
    header('Location: dashboard.php');
    exit;
}

// Set default dates
$today = date('Y-m-d');
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : $today;
$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'daily';

// Include header
$pageTitle = "Financial Reports";
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 animate-fade-in">Financial Reports</h1>
        <div>
            <button class="btn btn-success animate-on-hover" data-hover-animation="pulse" id="exportExcel">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
            <button class="btn btn-danger animate-on-hover" data-hover-animation="pulse" id="exportPDF">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </button>
        </div>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card shadow mb-4 animate-fade-in" style="animation-delay: 0.1s">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
        </div>
        <div class="card-body">
            <form method="get" id="reportFilterForm">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type">
                            <option value="daily" <?php echo ($reportType == 'daily') ? 'selected' : ''; ?>>Daily Report</option>
                            <option value="weekly" <?php echo ($reportType == 'weekly') ? 'selected' : ''; ?>>Weekly Report</option>
                            <option value="monthly" <?php echo ($reportType == 'monthly') ? 'selected' : ''; ?>>Monthly Report</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-primary w-100 animate-on-hover" data-hover-animation="pulse">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4 animate-fade-in" style="animation-delay: 0.2s">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Chart</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4 animate-fade-in" style="animation-delay: 0.3s">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Summary</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tbody id="summaryTable">
                                <!-- Summary data will be loaded via AJAX -->
                                <tr>
                                    <td>Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4 animate-fade-in" style="animation-delay: 0.4s">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service Type Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-2">
                        <canvas id="serviceTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 animate-fade-in" style="animation-delay: 0.5s">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detailed Report</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reportsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                            <th>Avg. Order Value</th>
                            
                        </tr>
                    </thead>
                    <tbody id="reportData">
                        <!-- Report data will be loaded via AJAX -->
                        <tr>
                            <td colspan="5" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Excel Export
    document.getElementById('exportExcel').addEventListener('click', function() {
        const wb = XLSX.utils.book_new();
        wb.Props = {
            Title: "TIBA LAUNDRY EXPRESS Financial Report",
            Subject: "Financial Report",
            Author: "TIBA LAUNDRY",
            CreatedDate: new Date()
        };

        // Create header row
        const header = ['Date', 'Orders', 'Revenue', 'Avg. Order Value'];

        // Prepare the data
        const rows = [];
        document.querySelectorAll("#reportsTable tbody tr").forEach((row) => {
            const rowData = [];
            row.querySelectorAll("td").forEach((cell) => {
                rowData.push(cell.innerText);
            });
            if (rowData.length > 0) {
                rows.push(rowData);
            }
        });

        // Add summary data at the top
        const summaryData = [];
        summaryData.push(['TIBA LAUNDRY EXPRESS - Financial Report']);
        summaryData.push(['Report Period:', `${document.getElementById('start_date').value} to ${document.getElementById('end_date').value}`]);
        summaryData.push(['Report Type:', document.getElementById('report_type').options[document.getElementById('report_type').selectedIndex].text]);
        summaryData.push(['Generated On:', new Date().toLocaleDateString()]);
        summaryData.push([]);  // Empty row as separator

        // Get summary table data if available
        const summaryTable = document.querySelectorAll("#summaryTable tr");
        if (summaryTable.length > 0) {
            summaryData.push(['SUMMARY']);
            summaryTable.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 2) {
                    summaryData.push([cells[0].innerText, cells[1].innerText]);
                }
            });
            summaryData.push([]);  // Empty row as separator
        }
        
        // Add the header to the data rows
        const fullData = [...summaryData, ['DETAILED REPORT'], header, ...rows];

        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet(fullData);
        
        // Set column widths
        const colWidths = [
            { wch: 15 },  // Date
            { wch: 10 },  // Orders
            { wch: 15 },  // Revenue
            { wch: 18 },  // Avg. Order Value
            
        ];
        ws['!cols'] = colWidths;

        // Apply styles
        // Function to get cell reference (e.g., "A1")
        const getCellRef = (rowIndex, colIndex) => {
            const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            return alphabet.charAt(colIndex) + (rowIndex + 1);
        };

        // Define styles with brighter colors for title
        const titleStyle = { 
            font: { bold: true, color: { rgb: "FFFFFF" }, sz: 16 },
            fill: { fgColor: { rgb: "FF0000" } },  // Bright red background
            alignment: { horizontal: "center", vertical: "center" }
        };
        
        const subtitleStyle = { 
            font: { bold: true, color: { rgb: "000000" } },
            fill: { fgColor: { rgb: "FFFF00" } }  // Yellow background
        };
        
        const headerStyle = { 
            font: { bold: true, color: { rgb: "FFFFFF" } },
            fill: { fgColor: { rgb: "0070C0" } },  // Bright blue
            alignment: { horizontal: "center" }
        };
        
        const summaryLabelStyle = { 
            font: { bold: true },
            fill: { fgColor: { rgb: "92D050" } }  // Green
        };

        const alternateRowStyle = {
            fill: { fgColor: { rgb: "E6F0FF" } }  // Light blue
        };

        // Apply title style with strong colors
        ws[getCellRef(0, 0)].s = titleStyle;
        
        // Merge cells for title
        if (!ws['!merges']) ws['!merges'] = [];
        ws['!merges'].push({ s: {r: 0, c: 0}, e: {r: 0, c: 4} });  // Merge A1:E1
        
        // Apply styles to the report info rows
        for (let i = 1; i < 4; i++) {
            ws[getCellRef(i, 0)].s = subtitleStyle;
        }
        
        // Apply styles to the summary section title
        const summaryTitleRow = summaryData.length > 0 ? summaryData.findIndex(row => row[0] === 'SUMMARY') : -1;
        if (summaryTitleRow !== -1) {
            const row = summaryTitleRow;
            ws[getCellRef(row, 0)].s = titleStyle;  // Use the same bright red as title
            if (!ws['!merges']) ws['!merges'] = [];
            ws['!merges'].push({ s: {r: row, c: 0}, e: {r: row, c: 4} });  // Merge cells
        }
        
        // Apply styles to summary data
        if (summaryTitleRow !== -1) {
            for (let i = summaryTitleRow + 1; i < summaryData.length - 1; i++) {
                if (summaryData[i].length >= 1) {
                    ws[getCellRef(i, 0)].s = summaryLabelStyle;
                }
            }
        }
        
        // Style the detailed report title
        const detailedReportRow = summaryData.length;
        ws[getCellRef(detailedReportRow, 0)].s = titleStyle;  // Use the same bright red as title
        if (!ws['!merges']) ws['!merges'] = [];
        ws['!merges'].push({ s: {r: detailedReportRow, c: 0}, e: {r: detailedReportRow, c: 4} });  // Merge cells
        
        // Apply header style
        const headerRow = summaryData.length + 1;
        for (let i = 0; i < header.length; i++) {
            ws[getCellRef(headerRow, i)].s = headerStyle;
        }
        
        // Apply alternating row colors to data rows
        for (let i = 0; i < rows.length; i++) {
            if (i % 2 === 1) {  // Apply to odd rows
                for (let j = 0; j < 5; j++) {  // Apply to all 5 columns
                    const cellRef = getCellRef(headerRow + 1 + i, j);
                    ws[cellRef].s = alternateRowStyle;
                }
            }
        }

        // Create a sheet and add it to the workbook
        XLSX.utils.book_append_sheet(wb, ws, "Financial Report");

        // Export to Excel
        XLSX.writeFile(wb, "TIBA_LAUNDRY_EXPRESS_Financial_Report.xlsx");
        
    });

    // PDF Export - Fixed by checking if the library is properly loaded
    document.getElementById('exportPDF').addEventListener('click', function() {
        // Check if jsPDF is properly loaded
        if (typeof window.jspdf !== 'undefined' && typeof window.jspdf.jsPDF !== 'undefined') {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Add title
            doc.setFontSize(18);
            doc.setTextColor(255, 0, 0);  // Red
            doc.text("TIBA LAUNDRY EXPRESS - Financial Report", doc.internal.pageSize.getWidth() / 2, 20, { align: "center" });
            
            // Add report info
            doc.setFontSize(11);
            doc.setTextColor(0);
            doc.text(`Report Period: ${document.getElementById('start_date').value} to ${document.getElementById('end_date').value}`, 14, 30);
            doc.text(`Report Type: ${document.getElementById('report_type').options[document.getElementById('report_type').selectedIndex].text}`, 14, 37);
            doc.text(`Generated On: ${new Date().toLocaleDateString()}`, 14, 44);
            
            // Add summary data if available
            let yPosition = 55;
            const summaryTable = document.querySelectorAll("#summaryTable tr");
            if (summaryTable.length > 0) {
                doc.setFontSize(14);
                doc.setTextColor(255, 0, 0);  // Red
                doc.text("Summary", 14, yPosition);
                yPosition += 7;
                
                doc.setFontSize(11);
                doc.setTextColor(0);
                summaryTable.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 2) {
                        doc.text(`${cells[0].innerText}: ${cells[1].innerText}`, 14, yPosition);
                        yPosition += 7;
                    }
                });
                yPosition += 5;
            }
            
            // Prepare data for detailed report table
            const header = [['Date', 'Orders', 'Revenue', 'Avg. Order Value']];
            const data = [];
            document.querySelectorAll("#reportsTable tbody tr").forEach((row) => {
                const rowData = [];
                row.querySelectorAll("td").forEach((cell) => {
                    rowData.push(cell.innerText);
                });
                if (rowData.length > 0) {
                    data.push(rowData);
                }
            });
            
            // Add detailed report table
            doc.setFontSize(14);
            doc.setTextColor(255, 0, 0);  // Red
            doc.text("Detailed Report", 14, yPosition);
            yPosition += 7;
            
            // Create the table
            doc.autoTable({
                startY: yPosition,
                head: header,
                body: data,
                theme: 'grid',
                headStyles: {
                    fillColor: [0, 112, 192],  // Blue
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    halign: 'center'
                },
                alternateRowStyles: {
                    fillColor: [230, 240, 255]  // Light blue
                },
                columnStyles: {
                    0: { cellWidth: 25 },  // Date
                    1: { cellWidth: 20 },  // Orders
                    2: { cellWidth: 25 },  // Revenue
                    3: { cellWidth: 30 },  // Avg. Order Value
                    
                },
                margin: { top: 10 }
            });
            
            // Save the PDF
            doc.save("TIBA_LAUNDRY_EXPRESS_Financial_Report.pdf");
        } else {
            // Alert if jsPDF is not loaded properly
            alert("PDF export library not loaded properly. Please check your internet connection and try again.");
            console.error("jsPDF library not available. Make sure it's properly loaded.");
        }
    });
});
// PDF Export with fixed width issue
document.getElementById('exportPDF').addEventListener('click', function() {
    // Check if jsPDF is properly loaded
    if (typeof window.jspdf !== 'undefined' && typeof window.jspdf.jsPDF !== 'undefined') {
        const { jsPDF } = window.jspdf;
        
        // Create PDF in landscape orientation with more space
        const doc = new jsPDF({
            orientation: 'landscape', // Change to landscape for more width
            unit: 'mm',
            format: 'a4'
        });
        
        // Add title
        doc.setFontSize(18);
        doc.setTextColor(255, 0, 0);  // Red
        doc.text("TIBA LAUNDRY EXPRESS - Financial Report", doc.internal.pageSize.getWidth() / 2, 20, { align: "center" });
        
        // Add report info
        doc.setFontSize(11);
        doc.setTextColor(0);
        doc.text(`Report Period: ${document.getElementById('start_date').value} to ${document.getElementById('end_date').value}`, 14, 30);
        doc.text(`Report Type: ${document.getElementById('report_type').options[document.getElementById('report_type').selectedIndex].text}`, 14, 37);
        doc.text(`Generated On: ${new Date().toLocaleDateString()}`, 14, 44);
        
        // Add summary data if available
        let yPosition = 55;
        const summaryTable = document.querySelectorAll("#summaryTable tr");
        if (summaryTable.length > 0) {
            doc.setFontSize(14);
            doc.setTextColor(255, 0, 0);  // Red
            doc.text("Summary", 14, yPosition);
            yPosition += 7;
            
            doc.setFontSize(11);
            doc.setTextColor(0);
            summaryTable.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 2) {
                    doc.text(`${cells[0].innerText}: ${cells[1].innerText}`, 14, yPosition);
                    yPosition += 7;
                }
            });
            yPosition += 5;
        }
        
        // Prepare data for detailed report table
        const header = [['Date', 'Orders', 'Revenue', 'Avg. Order Value']];
        const data = [];
        
        // Process table data and truncate long text in Details column to prevent overflow
        document.querySelectorAll("#reportsTable tbody tr").forEach((row) => {
            const rowData = [];
            row.querySelectorAll("td").forEach((cell, index) => {
                // If this is the Details column (index 4), truncate if too long
                if (index === 4) {
                    const text = cell.innerText;
                    // Limit the Details column text to prevent overflow
                    if (text.length > 80) {
                        rowData.push(text.substring(0, 77) + '...');
                    } else {
                        rowData.push(text);
                    }
                } else {
                    rowData.push(cell.innerText);
                }
            });
            if (rowData.length > 0) {
                data.push(rowData);
            }
        });
        
        // Add detailed report table
        doc.setFontSize(14);
        doc.setTextColor(255, 0, 0);  // Red
        doc.text("Detailed Report", 14, yPosition);
        yPosition += 7;
        
        // Create the table with adjusted column widths
        doc.autoTable({
            startY: yPosition,
            head: header,
            body: data,
            theme: 'grid',
            headStyles: {
                fillColor: [0, 112, 192],  // Blue
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center'
            },
            alternateRowStyles: {
                fillColor: [230, 240, 255]  // Light blue
            },
            columnStyles: {
                0: { cellWidth: 30 },   // Date - slightly wider
                1: { cellWidth: 20 },   // Orders
                2: { cellWidth: 25 },   // Revenue
                3: { cellWidth: 30 },   // Avg. Order Value
                
            },
            margin: { top: 10, left: 10, right: 10 },
            styles: {
                overflow: 'linebreak',  // Handle overflow with line breaks
                cellPadding: 2,         // Reduce padding to fit more content
                fontSize: 9             // Slightly smaller font
            }
        });
        
        // Save the PDF
        doc.save("TIBA_LAUNDRY_EXPRESS_Financial_Report.pdf");
    } else {
        // Alert if jsPDF is not loaded properly
        alert("PDF export library not loaded properly. Please check your internet connection and try again.");
        console.error("jsPDF library not available. Make sure it's properly loaded.");
    }
});

</script>

<script src="assets/js/reports.js"></script>

<?php include 'includes/footer.php'; ?>