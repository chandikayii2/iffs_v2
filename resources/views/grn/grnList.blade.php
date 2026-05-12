@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>GRN LIST</h4>
                <h6>Manage your Grn</h6>
            </div>
            <div class="page-btn">
                @foreach ($getLoginUserPermission as $check)
                    @if ($check->slug === 'add_grn')
                        <!-- Display the "Add New Grn" button if user has permission -->
                        <a href="{{ route('create-grn-view') }}" class="btn btn-added">
                            <img src="/assets/admin/img/icons/plus.svg" alt="img">Add New Grn
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-path">
                            <a class="btn btn-filter" id="filter_search">
                                <img src="/assets/admin/img/icons/filter.svg" alt="img">
                            </a>
                        </div>
                        <div class="search-input">
                            <a class="btn btn-searchset"><img src="/assets/admin/img/icons/search-white.svg"
                                    alt="img"></a>
                        </div>
                    </div>
                </div>

                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                        </div>
                    </div>
                </div>


                <!--products  Modal -->
                <div class="modal fade" id="grnViewModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="static"
                    data-keyboard="false">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Grn Products </h5>
                                <button type="button" id="closeModalButton" class="close" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body mb-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><strong>Product Code</strong></th>
                                            <th><strong>Product Name</strong></th>
                                            <th class="text-center"><strong>Grn Quantity</strong></th>
                                            <th class="text-center"><strong>Serial Number</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody class="details_body">

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- products model End -->



                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th style="display:none">
                                    <label class="checkboxs">
                                        <input type="checkbox" id="select-all">
                                        <span class="checkmarks"></span>
                                    </label>
                                </th>
                                <th>Grn No</th>
                                <th class="text-right">Purchase Order No</th>
                                <th class="text-center">Grn Date</th>
                                <th class="text-center">Reference</th>
                                {{-- <th class="text-center">Status</th> --}}
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grns as $grn)
                                <tr>
                                    <td style="display:none">
                                        <!-- Your checkbox code here -->
                                    </td>
                                    <td>{{ $grn->grn_number }}</td>
                                    <td class="text-right">{{ $grn->purchase_order_number }}</td>
                                    <td class="text-center">{{ $grn->grn_date }}</td>
                                    <td class="text-center">{{ $grn->reference }}</td>
                                    {{-- <td class="text-center">{{ $grn->status }}</td> --}}
                                    <td class="text-center">


                                        <!-- Action buttons (e.g., edit, delete) -->
                                        <a type="button" class="grn_products" data-id="{{ $grn->id }}"
                                            data-toggle="modal" data-target="#grnViewModal">
                                            <img src="/assets/admin/img/icons/eye.svg" alt="img">
                                        </a>
                                        &nbsp;

                                        @foreach ($getLoginUserPermission as $check)
                                            @if ($check->slug === 'delete_grn')
                                                @php
                                                    $outQuantity = DB::table('grn_products')
                                                        ->where('grn_id', $grn->id)
                                                        ->whereNotNull('out_quantity') // Check if any out_quantity is not null
                                                        ->exists(); // Check if any such record exists
                                                @endphp
                                                <!-- Display the delete icon only if out quantity is null -->
                                                @if (!$outQuantity)
                                                    <!-- Negating the condition -->
                                                    <a type="button" class="grn_delete" data-id="{{ $grn->id }}"
                                                        data-toggle="modal">
                                                        <img src="/assets/admin/img/icons/delete.svg" alt="Delete">
                                                    </a>
                                                    &nbsp;
                                                @endif
                                            @endif
                                        @endforeach

                                       <!-- @foreach ($getLoginUserPermission as $check)
                                            @if ($check->slug === 'delete_grn')
                                                @php
                                                    $outQuantity = DB::table('grn_products')
                                                        ->where('grn_id', $grn->id)
                                                        ->value('out_quantity');
                                                @endphp-->
                                                <!-- Display the delete icon only if out quantity is not null or 0 -->
                                              <!--  @if ($outQuantity == null && $outQuantity == 0)
                                                    <a type="button" class="grn_delete" data-id="{{ $grn->id }}"
                                                        data-toggle="modal">
                                                        <img src="/assets/admin/img/icons/delete.svg" alt="Delete">
                                                    </a>
                                                    &nbsp;
                                                @endif
                                            @endif
                                        @endforeach-->

                                        <a type="button" href="{{ url('admin/grn/generate-pdf/' . $grn->id) }}"
                                            class="pdf_products">
                                            <img src="/assets/admin/img/icons/pdf.svg" alt="img">
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
</div>


<script>
    $('#closeModalButton').click(function() {
        $('#editModal').modal('hide');
    });
</script>

{{-- delete po --}}
<script>
    $(".grn_delete").click(function() {
        var grn_id = $(this).data("id");

        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to Delete this?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: 'DELETE',
                    url: "/admin/grn/delete-grn/" + grn_id,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    cache: false,
                    success: function(data) {
                        // Handle success response
                        if (data.status == 200) {
                            toastr.options = {
                                "closeButton": true,
                                "progressBar": true
                            }
                            toastr.success(data.message);
                        } else if (data.status == 400) {
                            toastr.options = {
                                "closeButton": true,
                                "progressBar": true
                            }
                            toastr.error(data.message);
                        }
                        $(".grn_delete").prop("disabled", false);
                        setTimeout(function() {
                            window.location.href =
                                "{{ url('admin/grn') }}";
                        }, 1500);
                    },
                    error: function(e) {
                        // Handle error response
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.error(e);
                        $(".grn_delete").prop("disabled", false);
                    }
                });
            }
        });

    });
</script>


<script>
    $(".grn_products").click(function() {
        var grn_id = $(this).data("id");

        $.ajax({
            method: 'GET',
            dataType: "json",
            url: "/admin/grn/grn_products_view/" + grn_id,
            cache: false,
            success: function(data) {
                let dataArray = data.data;
                console.log(dataArray);

                // Clear previous data in the modal body
                $('.details_body').empty();

                // Loop through the retrieved grnProducts
                dataArray.forEach(function(product) {
                    var row = '<tr>' +
                        '<td>' + product.product_code + '</td>' +
                        '<td>' + product.product_name + '</td>' +
                        '<td class="text-center">' + product.received_quantity + '</td>' +
                        '<td class="text-center">';

                    // Check if grnSerialNumbers exist
                    if (product.grn_serial_numbers && product.grn_serial_numbers.length >
                        0) {
                        // Loop through grnSerialNumbers
                        product.grn_serial_numbers.forEach(function(serial) {
                            row += serial.serial_number + '<br>';
                        });
                    } else {
                        row += 'No serial numbers';
                    }

                    row += '</td></tr>';

                    // Append row to the modal table
                    $('.details_body').append(row);
                });

            },
            error: function(e) {
                console.log(e);
            },
        });
    });
</script>



</body>

</html>
