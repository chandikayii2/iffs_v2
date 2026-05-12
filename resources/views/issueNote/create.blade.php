@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Issue Note Add</h4>
                <h6>Add / Update Issue Note</h6>
            </div>
        </div>
        <form id="form">
            <div class="card" id="po-message">
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Issue Note No</label>
                                <input type="text" name="issue_note_number" id="issue_note_number"
                                    value="{{ $newIssueNoteNo }}" readonly>
                            </div>

                        </div>


                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Issue Note Date</label>
                                <div class="input-groupicon">
                                    <input type="text" name="issue_note_date" id="issue_note_date"
                                        placeholder="DD-MM-YYYY" class="datetimepicker" required>
                                    <div class="addonset">
                                        <img src="/assets/admin/img/icons/calendars.svg" alt="img">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Lorry Number</label>
                                <input type="text" name="lorry_number" id="lorry_number" required placeholder="Enter...">
                                <small class="text-danger" id="lorry_number_error" style="display: none;">Lorry Number is required</small>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Driver Name</label>
                                <input type="text" name="driver_name" id="driver_name" required placeholder="Enter...">
                                <small class="text-danger" id="driver_name_error" style="display: none;">Driver Name is required</small>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <input type="text" name="remarks" id="remarks" placeholder="Enter...">
                            </div>
                        </div>


                        <div class="col-lg-5 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Product Name</label>
                                <div class="row">
                                    <div class="col-lg-12 col-sm-10 col-10">
                                        <select class="select" id="product" name="product">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->product_code }} - {{ $product->product_name }}
                                                    <!-- Separate code and name with a hyphen -->
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Serial Number</label>
                                <div class="row">
                                    <div class="col-lg-12 col-sm-10 col-10">
                                        <select class="select" id="serial_number" name="serial_number">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Stock quantity</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" placeholder="Enter..."
                                    readonly>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Issue quantity</label>
                                <input type="number" name="issue_quantity" id="issue_quantity" placeholder="Enter...">

                            </div>
                        </div>

                        <input type="hidden" name="grn_ids" id="grn_ids" placeholder="Enter...">

                        <div class="col-lg-12 mb-3 col-sm-6 col-12 d-flex justify-content-end">
                            <button type="button" id="ok-button" class="btn btn-submit me-2">Ok</button>
                        </div>



                    </div>

                    <div class="row">
                        <div class="table-responsive">
                            <table class="table" id="product-details-table">
                                <thead>
                                    <tr>
                                        <th style="display:none;">Id</th>
                                        <th style="font-weight: bold;">Product Code</th>
                                        <th style="font-weight: bold;">Product Name</th>
                                        <th style="font-weight: bold;">Serial No</th>
                                        <th style="text-align: center; font-weight: bold;">Quantity</th>
                                        <th style="display:none;">serial Numbers</th>
                                        <th style="display:none;">grn Ids</th>
                                        <th style="text-align: center; font-weight: bold;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">

                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>

                        </div>
                    </div>


                    <div class="row ">
                        <div class="col-lg-12 float-md-right">
                            <div class="total-order">
                                <ul>

                                </ul>
                            </div>
                        </div>
                    </div>



                    <div class="col-lg-12">
                        <button type="button" id="issue-submit-button" class="btn btn-submit me-2">Submit</button>
                    </div>
                </div>
            </div>
    </div>
</div>
</div>
</div>
</form>


<script>
    // get today's date
    var today = new Date();
    // get the day, month, and year
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    // combine into the desired date format
    var todayFormatted = dd + '-' + mm + '-' + yyyy;
    // set the input value to today's date
    document.getElementById("issue_note_date").value = todayFormatted;
</script>

<script>
    $(function() {
        $("select").select2();
    });
</script>


<script>
    $(document).ready(function() {

        $('#serial_number').prop('disabled', true);

        $('#product').change(function() {
            var productId = $(this).val();
            //alert(productId);
            if (productId != '') {
                var url = '/admin/issue-note/get-product-data/' + productId;
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let product = data.data;
                        console.log(product);
                        //console.log(product.grn_id);

                        // Update stock quantity input field
                        $('#stock_quantity').val(product.stock_quantity);
                        $('#grn_ids').val(product.grn_id);

                        // Populate serial number dropdown
                        var serialNumberDropdown = $('#serial_number');
                        serialNumberDropdown.empty(); // Clear existing options

                        if (product.serial_numbers.length > 0) {
                            $('#serial_number').prop('disabled', false);

                            // Add empty option (placeholder)
                            serialNumberDropdown.append($('<option>', {
                                value: '',
                                text: ''
                            }));

                            // Populate dropdown with serial numbers
                            $.each(product.serial_numbers, function(index, serialNumber) {
                                serialNumberDropdown.append($('<option>', {
                                    value: serialNumber.id,
                                    text: serialNumber.serial_number
                                }));
                            });

                            // Convert select element to multi-select
                            serialNumberDropdown.attr('multiple', 'multiple');

                            // Initialize Select2
                            serialNumberDropdown.addClass('select2');
                            serialNumberDropdown.select2({
                                placeholder: "",
                                allowClear: true
                            });

                            // Event listener for selection change
                            serialNumberDropdown.on('change', function() {
                                var selectedSerials = $(this).val();
                                if (selectedSerials && selectedSerials.length > 0) {
                                    // Exclude the empty option from the count
                                    var selectedCount = selectedSerials.filter(
                                        function(serial) {
                                            return serial !== '';
                                        }).length;
                                    $('#issue_quantity').val(selectedCount);
                                } else {
                                    $('#issue_quantity').val('');
                                }
                            });
                        } else {
                            $('#serial_number').prop('disabled', true);
                            $('#serial_number').removeAttr('multiple').removeClass(
                                'select2').empty();
                            $('#issue_quantity').val(
                                ''); // Clear issue quantity if no serial numbers available
                        }

                        // Check if serial number field is disabled
                        if ($('#serial_number').prop('disabled')) {
                            $('#issue_quantity').prop('readonly', false);
                        } else {
                            $('#issue_quantity').prop('readonly', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        // Function to validate required fields
        function validateRequiredFields() {
            var isValid = true;
            
            // Reset error messages
            $('#lorry_number_error').hide();
            $('#driver_name_error').hide();
            
            // Validate Lorry Number
            var lorryNumber = $('#lorry_number').val();
            if (!lorryNumber || lorryNumber.trim() === '') {
                $('#lorry_number_error').show();
                $('#lorry_number').focus();
                isValid = false;
            }
            
            // Validate Driver Name
            var driverName = $('#driver_name').val();
            if (!driverName || driverName.trim() === '') {
                $('#driver_name_error').show();
                if (isValid) {
                    $('#driver_name').focus();
                }
                isValid = false;
            }
            
            return isValid;
        }

        // Event listener for Ok button click
        $('#ok-button').click(function() {
            // Validate required fields
            if (!validateRequiredFields()) {
                return;
            }

            var productId = $('#product').val();
            var productFullText = $('#product option:selected').text().trim();
            var stockQuantity = $('#stock_quantity').val();
            var issueQuantity = $('#issue_quantity').val();
            var grn_ids = $('#grn_ids').val();

            // Check if the product already exists in the table
            var existingProductIds = $('#productTableBody').find('td:first-child').map(function() {
                return $(this).text();
            }).get();

            if (existingProductIds.includes(productId)) {
                alert("This product is already added to the table.");
                return;
            }

            var serialNumberData = []; // Array to store serial number data

            // Get the selected serial numbers and their IDs
            $('#serial_number option:selected').each(function() {
                var serialId = $(this).val();
                var serialNumber = $(this).text();
                // Check if serialNumber is not empty before pushing it to the array
                if (serialNumber.trim() !== '') {
                    serialNumberData.push({
                        id: serialId,
                        serial_number: serialNumber
                    });
                }
            });

            // Check if any field is empty, only check serial numbers if the field is not disabled
            if (!productId || (!$('#serial_number').prop('disabled') && !serialNumberData) || !
                stockQuantity || !issueQuantity) {
                alert("Please fill in all fields.");
                return;
            }

            // Check if issue quantity exceeds stock quantity
            if (parseInt(issueQuantity) > parseInt(stockQuantity)) {
                alert("Issue quantity cannot exceed stock quantity.");
                return;
            }

            // Split the selected option text to extract product code and name
            var productNameParts = productFullText.split(' - ');
            var productCode = productNameParts[0]; // First part is always the product code
            
            // The rest parts form the product name (handle both 2-part and 3-part names)
            var productName = productNameParts.slice(1).join(' - ');

            // Create a new table row
            var newRow = $('<tr>');

            // Add data to the row
            newRow.append($('<td>').text(productId).css('display', 'none')); // Product ID
            newRow.append($('<td>').text(productCode)); // Product Code
            newRow.append($('<td>').text(productName)); // Product Name

            // Initialize an array to store serial number data in JSON format
            var serialNumbersArray = [];

            // Loop through each serial number data
            serialNumberData.forEach(function(serialData) {
                // Construct an object for each serial number data
                var serialObject = {
                    id: serialData.id,
                    serial_number: serialData.serial_number
                };
                // Push the serial number object to the array
                serialNumbersArray.push(serialObject);
            });

            console.log(serialNumbersArray);
            // Create a cell to store the serial numbers
            var serialNumbersCell = $('<td>');

            // Loop through each serial number data again to append them to the cell
            serialNumberData.forEach(function(serialData) {
                serialNumbersCell.append(serialData.serial_number +
                    '<br>'); // Append each serial number
            });

            // Append the cell containing serial numbers to the row
            newRow.append(serialNumbersCell);

            // Append the issue quantity to the newRow and center the text
            newRow.append($('<td>').text(issueQuantity).css('text-align', 'center')); // Quantity

            // Append the JSON array to the newRow and hide the cell
            newRow.append($('<td>').text(JSON.stringify(serialNumbersArray)).css('display', 'none'));

            // Parse the array of GRN IDs as JSON
            var grnIdsArray = JSON.parse('[' + grn_ids + ']');
            // Stringify the parsed array to display in the desired format
            var grnIdsString = JSON.stringify(grnIdsArray);

            // Append the stringified GRN IDs to the newRow
            newRow.append($('<td>').text(grnIdsString).css('display', 'none'));

            // Create action buttons cell
            var actionsCell = $('<td>').css('text-align', 'center');
            var deleteButton = $('<button>').addClass('btn btn-danger delete-row').html(
                '<i class="fas fa-trash"></i>');
            actionsCell.append(deleteButton);
            newRow.append(actionsCell);

            // Append the new row to the table body
            $('#productTableBody').append(newRow);

            // Clear input fields after appending
            $('#product').val("");
            $("#product").select2("val", "");
            $("#product").select2();
            $('#serial_number').val("");
            $('#serial_number').trigger('change'); // Trigger change event to clear selections
            $('#stock_quantity').val('');
            $('#issue_quantity').val('');
            $('#serial_number').prop('disabled', true);
        });

        // Event listener for delete button click (using event delegation)
        $('#productTableBody').on('click', '.delete-row', function() {
            // Ask for confirmation before deleting the row
            if (confirm("Are you sure you want to delete this?")) {
                // If user confirms, remove the row
                $(this).closest('tr').remove();
            }
        });

        $('#issue-submit-button').click(function() {
            // Validate required fields before submission
            if (!validateRequiredFields()) {
                return;
            }

            // Get the values from the form fields
            var issueNoteNumber = $('#issue_note_number').val();
            var issueNoteDate = $('#issue_note_date').val();
            var lorryNumber = $('#lorry_number').val();
            var driverName = $('#driver_name').val();
            var remarks = $('#remarks').val();

            console.log(issueNoteNumber);

            // Get the selected Product ID
            var productId = $('#product').val();

            // Check if a Product ID is selected and the table body is empty
            if (!productId && $('#product-details-table tbody tr').length === 0) {
                alert("Please select a product.");
                return;
            }

            // Check if the table body is empty
            if ($('#product-details-table tbody tr').length === 0) {
                alert("The table is empty. Please add products before submitting.");
                return;
            }

            var issue_products = [];

            $('#product-details-table tbody tr').each(function() {
                var rowData = {};

                rowData.productId = $(this).find('td:eq(0)').text();

                var serialNumbersArray = JSON.parse($(this).find('td:eq(5)').text());
                rowData.serial_numbers = serialNumbersArray;

                rowData.issueQuantity = $(this).find('td:eq(4)').text();

                var grnIdsArray = JSON.parse($(this).find('td:eq(6)').text());
                rowData.grn_ids = grnIdsArray;

                issue_products.push(rowData);
            });

            console.log(issue_products);

            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to Issue this?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '{{ route('create-issue-note') }}',
                        type: 'POST',
                        data: {
                            issue_note_number: issueNoteNumber,
                            issue_note_date: issueNoteDate,
                            lorry_number: lorryNumber,
                            driver_name: driverName,
                            remarks: remarks,
                            issue_products: issue_products

                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {

                            let d = data.data;
                            console.log(d);

                            if (data.status == 200) {
                                toastr.options = {
                                    "closeButton": true,
                                    "progressBar": true
                                }
                                toastr.success(data.message);
                                setTimeout(function() {
                                window.location.href =
                                    "{{ url('admin/issue-note/create') }}";
                            }, 1500);

                            $("#issue-submit-button").prop("disabled", false);

                            } else if (data.status == 400) {
                                toastr.options = {
                                    "closeButton": true,
                                    "progressBar": true
                                }
                                toastr.error(data.message);
                            }
                        },
                        error: function(e) {
                            console.log(e);
                        }
                    });
                }
            });
        });
    });
</script>