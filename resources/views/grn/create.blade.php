@include('layouts.header')
<style>
    /* Custom checkbox styling */
    #uniqueCheckbox {
        width: 20px;
        /* Adjust width as needed */
        height: 20px;
        /* Adjust height as needed */
        margin-left: 35px;
    }
</style>
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Grn Add</h4>
                {{-- <h6>Add / Update Grn</h6> --}}
            </div>
        </div>
        <form id="form">
            <div class="card" id="po-message">
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Purchase Order Number</label>
                                <div class="row">
                                    <div class="col-lg-12 col-sm-10 col-10">
                                        <select class="select" id="purchase_order_id" name="purchase_order_id">
                                            <option value="">Select Po Num</option>
                                            @foreach ($poNumbers as $id => $poNumber)
                                                <option value="{{ $id }}">{{ $poNumber }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Grn No</label>
                                <input type="text" name="grn_no" id="grn_no" value="{{ $newGrnNo }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Reference</label>
                                <input type="text" name="reference" id="reference" placeholder="Enter...">
                            </div>
                        </div>


                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Grn Date</label>
                                <div class="input-groupicon">
                                    <input type="text" name="grn_date" id="grn_date" placeholder="DD-MM-YYYY"
                                        class="datetimepicker" required>
                                    <div class="addonset">
                                        <img src="/assets/admin/img/icons/calendars.svg" alt="img">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-lg-4 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Product Name</label>
                                <div class="row">
                                    <div class="col-lg-12 col-sm-10 col-10">
                                        <select class="select" id="product" name="product">
                                            <option value="">--Products--</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Po quantity</label>
                                <input type="number" name="po_quantity" id="po_quantity" placeholder="Enter..."
                                    readonly>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Grn quantity</label>
                                <input type="number" name="grn_quantity" id="grn_quantity" placeholder="Enter...">
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Po price</label>
                                <input type="number" name="po_price" id="po_price" placeholder="Enter..." readonly>
                            </div>
                        </div>



                        <div class="col-lg-2 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Grn price</label>
                                <input type="number" name="grn_price" id="grn_price" placeholder="Enter...">
                            </div>
                        </div>

                        <input type="hidden" name="pro_id" id="pro_id">

                        <div class="col-lg-12 col-sm-6 col-12 d-flex justify-content-end">
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
                                        <th style="text-align: center; font-weight: bold;">Serial Numbers</th>

                                        <th style="text-align: center; font-weight: bold;">Quantity</th>
                                        <th style="text-align: center; font-weight: bold;">Unit Price</th>

                                        <th style="text-align: right; font-weight: bold;">Total</th>
                                        <th style="display:none;">Product id</th>

                                        <!-- Added Serial Numbers header -->
                                        <th style="text-align: center; font-weight: bold;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">

                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="font-weight: bold;" class="text-right ">Total:</td>
                                        <td colspan="2" style="text-align:right;font-weight: bold;" id="totalAmount">
                                            0</td>
                                    </tr>
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

                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document" style="max-width: 520px;">
                            <!-- Adjust the max-width to your desired size in pixels -->
                            <div class="modal-content">
                                <div class="modal-header container">
                                    <h5 class="modal-title" id="exampleModalLabel">Serial Number##</h5>

                                    <input type="checkbox" checked id="uniqueCheckbox"><span>Unique</span>

                                    <button type="button" id="closeModalButton" class="close" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="serialNumberInputs">
                                    <!-- Serial number input fields will be dynamically generated here -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="closeModalButton" class="btn btn-secondary"
                                        data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="confirmModal">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <button type="button" id="grn-submit-button" class="btn btn-submit me-2">Submit</button>
                    </div>
                </div>
            </div>



    </div>
</div>
</div>
</div>
</form>


<script>
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    var todayFormatted = dd + '-' + mm + '-' + yyyy;
    document.getElementById("grn_date").value = todayFormatted;
</script>

<script>
    $(function() {
        $("select").select2();
    });
</script>

<script>
    $(document).ready(function() {
        var productsData; // Declare the variable in a wider scope

        $('#purchase_order_id').on('change', function() {
            var purchaseOrderId = $(this).val();

            // Clear the Po Quantity and GRN Quantity input fields
            $('#po_quantity').val('');
            $('#grn_quantity').val('');
            $('#po_price').val('');
            $('#grn_price').val('');
            $('#pro_id').val('');

            $.ajax({
                url: '/admin/grn/get-purchase-order-products/' + purchaseOrderId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    productsData = data.data; // Assign the value to the variable
                    console.log(productsData); // Just for debugging

                    // Clear existing options
                    $('#product').empty();
                    // Add a default option
                    $('#product').append('<option value="">Select Product</option>');
                    // Loop through products and add options to the dropdown
                    $.each(productsData, function(key, product) {
                        $('#product').append('<option value="' + product.id + '">' +
                            product.product_code + "\t" + "-" + "\t" +
                            product.product_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#product').on('change', function() {
            var productId = $(this).val();
            // Find the selected product in the productsData array
            var selectedProduct = productsData.find(product => product.id == productId);

            if (selectedProduct) {
                // Set the quantity value to the input field
                $('#po_quantity').val(selectedProduct.remaining_quantity);
                $('#grn_quantity').val(selectedProduct.remaining_quantity);
                $('#po_price').val(selectedProduct.unit_price);
                $('#grn_price').val(selectedProduct.unit_price);
                $('#pro_id').val(selectedProduct.product_id);
            } else {
                console.error('Selected product not found.');
            }
        });




        $('#ok-button').on('click', function() {
            // Get the selected product, PO quantity, GRN quantity, and GRN price
            var productId = $('#product').val();
            var poQuantity = parseFloat($('#po_quantity').val());
            var grnQuantity = parseFloat($('#grn_quantity').val());
            var grnPrice = parseFloat($('#grn_price').val());
            var proId = $('#pro_id').val();

            // Find the selected product in the productsData array
            var selectedProduct = productsData.find(product => product.id == productId);

            // Check if either GRN quantity or GRN price is empty
            if (!grnQuantity || !grnPrice) {
                alert('GRN quantity and price cannot be empty.');
                return; // Exit the function early
            }

            // Check if the GRN quantity exceeds the PO quantity
            if (grnQuantity > poQuantity) {
                alert('GRN quantity cannot exceed PO quantity.');
                return; // Exit the function early
            }


            // Clear the input fields
            $('#product').val("");
            $("#product").select2("val", "");
            $("#product").select2();
            $('#po_quantity').val('');
            $('#grn_quantity').val('');
            $('#po_price').val('');
            $('#grn_price').val('');
            $('#pro_id').val('');

            // Check if the product is already in the table
            var isAlreadyAdded = false;
            $('#product-details-table tbody tr').each(function() {
                var existingProductId = $(this).find('td:first').text();
                if (existingProductId == productId) {
                    isAlreadyAdded = true;
                    return false; // Exit the loop early if a match is found
                }
            });


            if (selectedProduct && grnQuantity && !isAlreadyAdded) {
                // Check if the serial number of the selected product is 1
                if (selectedProduct.serial_number == 1) {
                    // Clear previous input fields
                    $('#serialNumberInputs').empty();

                    // Calculate the number of rows needed
                    var numRows = Math.ceil(grnQuantity / 2);

                    // Generate input fields for serial numbers
                    for (let i = 0; i < numRows; i++) {
                        // Add a new row
                        $('#serialNumberInputs').append('<div class="row">');

                        // Add two input fields per row
                        for (let j = 0; j < 2; j++) {
                            var serialNumber = i * 2 + j + 1;
                            if (serialNumber <= grnQuantity) {
                                $('#serialNumberInputs').append(
                                    '<div class="col"><input type="text" name="serial_number" class="form-control mb-2" placeholder="Enter Serial Number ' +
                                    serialNumber + '"></div>');
                            }
                        }

                        // Close the row
                        $('#serialNumberInputs').append('</div>');
                    }

                    // Show the pop-up modal
                    $('#myModal').modal('show');


                    // Event listener for the Confirm button click
                    $('#confirmModal').off('click').on('click', function() {
                        // Check if any serial number field is empty
                        var isSerialNumberEmpty = false;
                        $('#serialNumberInputs input').each(function() {
                            if ($(this).val().trim() === "") {
                                isSerialNumberEmpty = true;
                                return false; // Exit the loop early if an empty serial number is found
                            }
                        });

                        // If any serial number field is empty, show an alert and prevent further action
                        if (isSerialNumberEmpty) {
                            alert("Please enter all serial numbers.");
                            return;
                        }

                        // Get the selected product ID
                        var productId = $('#product').val();

                        // Check if the product already exists in the table
                        var isAlreadyAdded = false;
                        $('#product-details-table tbody tr').each(function() {
                            var existingProductId = $(this).find('td:first').text();
                            if (existingProductId == productId) {
                                isAlreadyAdded = true;
                                return false; // Exit the loop early if a match is found
                            }
                        });

                        if (isAlreadyAdded) {
                            // Show an alert if the product is already added
                            alert('Selected product is already added to the table.');
                            return; // Exit the function early
                        }

                        // Initialize an array to store serial numbers
                        var serialNumbers = [];

                        // Iterate through each input field and store the value
                        $('#serialNumberInputs input').each(function() {
                            serialNumbers.push($(this).val());
                        });

                        // Check if the checkbox is checked for unique serial numbers
                        var isUnique = $('#uniqueCheckbox').is(':checked');

                        if (isUnique) {
                            // Check for duplicate serial numbers
                            var uniqueSerialNumbers = [...new Set(serialNumbers)];
                            if (serialNumbers.length !== uniqueSerialNumbers.length) {
                                alert("Duplicate serial numbers are not allowed.");
                                // Focus on the first input field with a duplicate serial number
                                $('#serialNumberInputs input').each(function() {
                                    var value = $(this).val();
                                    if (serialNumbers.indexOf(value) !== serialNumbers
                                        .lastIndexOf(value)) {
                                        $(this).focus();
                                        return false; // Exit the loop early after focusing
                                    }
                                });
                                return; // Exit the function early
                            }
                        }

                        // Join the serial numbers with commas and <br> tags
                        var serialNumbersHtml = '<td style="text-align: center;">' +
                            serialNumbers.join(',<br>') + '</td>';

                        // Append a new row to the table with the selected product details, GRN quantity, and GRN price
                        $('#product-details-table tbody').append('<tr class="product-row">' +
                            '<td style="display:none;">' + selectedProduct.id + '</td>' +
                            '<td>' + selectedProduct.product_code + '</td>' +
                            '<td>' + selectedProduct.product_name + '</td>' +
                            serialNumbersHtml +
                            '<td style="text-align: center;" class="quantity">' +
                            grnQuantity + '</td>' +
                            '<td style="text-align: center;" class="price">' + grnPrice +
                            '</td>' +
                            '<td style="text-align: right;" class="total">' + (parseFloat(
                                grnQuantity) * parseFloat(grnPrice)).toFixed(2) + '</td>' +
                            '<td style="display:none;">' + proId + '</td>' +
                            '<td style="text-align: center;"><button class="btn btn-danger btn-sm delete-row">Delete</button></td>' +
                            '</tr>');

                        // Call the function to recalculate totals
                        calculateTotals();

                        // Hide the modal
                        $('#myModal').modal('hide');
                    });


                } else {
                    // Append a new row to the table with the selected product details, GRN quantity, and GRN price
                    $('#product-details-table tbody').append('<tr class="product-row">' +
                        '<td style="display:none;">' + selectedProduct.id + '</td>' +
                        '<td>' + selectedProduct.product_code + '</td>' +
                        '<td>' + selectedProduct.product_name + '</td>' +
                        '<td></td>' + // Empty column for serial numbers
                        '<td style="text-align: center;" class="quantity">' + grnQuantity +
                        '</td>' +
                        '<td style="text-align: center;" class="price">' + grnPrice + '</td>' +
                        '<td style="text-align: right;" class="total">' + (parseFloat(grnQuantity) *
                            parseFloat(
                                grnPrice)).toFixed(2) + '</td>' +
                        '<td style="display:none;">' + proId + '</td>' +
                        '<td style="text-align: center;"><button class="btn btn-danger btn-sm delete-row">Delete</button></td>' +
                        '</tr>');

                    // Call the function to recalculate totals
                    calculateTotals();
                }
            } else if (isAlreadyAdded) {
                alert('Selected product is already added to the table.');
            } else {
                console.error('Selected product not found or GRN quantity missing.');
            }



        });


        // Event delegation for deleting a row
        $('#product-details-table').on('click', '.delete-row', function() {
            // Remove the row from the table
            $(this).closest('tr').remove();

            // Clear all input fields including serial number input fields
            $('#product').val("");
            $("#product").select2("val", "");
            $("#product").select2();
            $('#po_quantity').val('');
            $('#grn_quantity').val('');
            $('#po_price').val('');
            $('#grn_price').val('');
            $('#pro_id').val('');
            $('#serialNumberInputs').empty(); // Clear serial number input fields
        });


        // Function to calculate totals
        function calculateTotals() {
            var totalAmount = 0;

            // Iterate through each row in the table
            $('.product-row').each(function() {
                // Get the quantity and price values from the row
                var quantity = parseFloat($(this).find('.quantity').text());
                var price = parseFloat($(this).find('.price').text());

                // Calculate the total for the current row
                var totalForRow = quantity * price;
                $(this).find('.total').text(totalForRow.toFixed(2));

                // Add to the overall total
                totalAmount += totalForRow;
            });

            // Display the overall total
            $('#totalAmount').text(totalAmount.toFixed(2));
        }

        // Call the function whenever a row is added or deleted
        $(document).on('click', '#ok-button, .delete-row', function() {
            calculateTotals();
        });

        // Call the function when the page loads initially
        $(document).ready(function() {
            calculateTotals();
        });


    });
</script>


<script>
    $(document).ready(function() {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false
        });

        // Adjust the click event listener to target the specific close button
        $('#myModal').on('click', '#closeModalButton', function() {
            // Clear the input fields and serial number inputs when the modal is closed without confirming
            $('#product').val("");
            $("#product").select2("val", "");
            $("#product").select2();
            $('#po_quantity').val('');
            $('#grn_quantity').val('');
            $('#po_price').val('');
            $('#grn_price').val('');
            $('#pro_id').val('');
            $('#serialNumberInputs').empty(); // Clear serial number input fields

            $('#myModal').modal('hide');
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#grn-submit-button').click(function() {
            // Get the selected Purchase Order ID
            var purchase_order_id = $('#purchase_order_id').val();

            // Check if Purchase Order ID is selected
            if (purchase_order_id === "") {
                // If not selected, show an alert
                alert("Please select a Purchase Order Number.");
                return; // Exit the function early
            }

            // Check if the table body is empty
            if ($('#product-details-table tbody tr').length === 0) {
                // If the table body is empty, show an alert
                alert("The table is empty. Please add products before submitting.");
                return; // Exit the function early
            }

            // Get the values of GRN Number, Reference, and GRN Date
            var grn_number = $('#grn_no').val();
            var reference = $('#reference').val();
            var grn_date = $('#grn_date').val();

            // Initialize an array to store table data along with other fields
            var grn_products = [];

            // Iterate through each row in the table body
            $('#product-details-table tbody tr').each(function() {
                var rowData = {};
                // Get the data from specific columns in the current row
                rowData['pop_id'] = $(this).find('td:eq(0)').text(); // Id
                rowData['received_price'] = $(this).find('td:eq(5)').text(); // Unit Price
                rowData['product_id'] = $(this).find('td:eq(7)').text(); // product id
                rowData['received_quantity'] = $(this).find('td:eq(4)').text(); // Quantity
                // Extract serial numbers into an array
                var serialNumbers = $(this).find('td:eq(3)').text().split(',');
                rowData['serial_numbers'] = serialNumbers; // Serial Numbers array
                // Push the row data to the table data array
                grn_products.push(rowData);
            });

            // Log the filtered table data along with other fields to the console
            console.log("Purchase Order Id:", purchase_order_id);
            console.log("GRN No:", grn_number);
            console.log("Reference:", reference);
            console.log("GRN Date:", grn_date);
            console.log("GRN Products:", grn_products);


            // Perform further actions such as AJAX requests, etc.


            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to Add Grn?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '{{ route('create-grn') }}',
                        type: 'POST',
                        data: {
                            purchase_order_id: purchase_order_id,
                            grn_number: grn_number,
                            reference: reference,
                            grn_date: grn_date,
                            grn_products: grn_products
                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                         success: function(data) {
                    console.log(data);
                    
                    if (data.status == 200) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.success(data.message);
                        
                        setTimeout(function() {
                            window.location.href = "{{ url('admin/grn/create') }}";
                        }, 1500);
                    } else if (data.status == 400) {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.error(data.message); // This should show the error message
                    }
                    $("#add-grn-button").prop("disabled", false);
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors (network issues, server errors, etc.)
                    console.log('XHR:', xhr);
                    console.log('Status:', status);
                    console.log('Error:', error);
                    
                    let errorMessage = 'An error occurred while processing your request.';
                    
                    // Try to parse the response if it's JSON
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        } else if (response.error) {
                            errorMessage = response.error;
                        }
                    } catch (e) {
                        // If not JSON, use the status text
                        errorMessage = xhr.statusText || errorMessage;
                    }
                    
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true
                    }
                    toastr.error(errorMessage);
                    $("#add-grn-button").prop("disabled", false);
                }
           
                    });

                }
            });
        });
    });
</script>
