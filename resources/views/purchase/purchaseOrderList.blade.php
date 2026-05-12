@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>PURCHASE ORDER LIST</h4>
                <h6>Manage your purchases</h6>
            </div>
            <div class="page-btn">
                @if (isset($getLoginUserPermission))
                    @foreach ($getLoginUserPermission as $check)
                        @if ($check->slug === 'add_purchase_order')
                            <a href="{{ route('create-purchase-order-view') }}" class="btn btn-added">
                                <img src="/assets/admin/img/icons/plus.svg" alt="img">Add New Po
                            </a>
                        @endif
                    @endforeach
                @endif
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
                <div class="modal fade" id="proViewModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="static"
                    data-keyboard="false">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Purchase Order Products </h5>
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
                                            <th><strong>Code</strong></th>
                                            <th><strong>Product Name</strong></th>
                                            <th class="text-center"><strong>Qty</strong></th>
                                            {{-- <th class="text-center"><strong>Grn Qty</strong></th>
                                            <th class="text-center"><strong>Pending Qty</strong></th> --}}
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
                                <th>Po No</th>
                                <th class="text-right">Supplier</th>
                                <th class="text-center">Purchase Order Date</th>
                                <th class="text-center">Reference</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchase_orders as $po)
                                <tr>
                                    <td style="display:none">
                                        <label class="checkboxs">
                                            <input type="checkbox">
                                            <span class="checkmarks"></span>
                                        </label>
                                    </td>
                                    <td>{{ $po->purchase_order_number }}</td>
                                    <td class="">{{ $po->supplier_name }}</td>
                                    <td class="text-center">{{ $po->purchase_order_date }}</td>
                                    <td class="text-center">{{ $po->reference }}</td>

                                    <td class="text-center">
                                        @php
                                            $po_status = '';
                                            $pending_grn = DB::table('purchase_order_products')
                                                ->where('purchase_order_id', $po->id)
                                                ->where('grn_status', 0)
                                                ->count();
                                            if ($pending_grn > 0) {
                                                $po_status = 'Pending';
                                                $badge_class = 'badge-warning';
                                            } else {
                                                $po_status = 'Success';
                                                $badge_class = 'badge-success';
                                            }

                                            // Check if purchase_order_id exists in grn_products table
                                            $has_grn = DB::table('grns')
                                                ->where('purchase_order_id', $po->id)
                                                ->exists();
                                        @endphp

                                        <span class="badge {{ $badge_class }}">{{ $po_status }}</span>
                                    </td>
                                    <td class="text-center">

                                        <a type="button" class="po_products" data-id="{{ $po->id }}"
                                            data-toggle="modal" data-target="#proViewModal">
                                            <img src="/assets/admin/img/icons/eye.svg" alt="img">
                                        </a>
                                        &nbsp;

                                         @if (isset($getLoginUserPermission))
                                            @foreach ($getLoginUserPermission as $check)
                                                @if ($check->slug === 'edit_purchase_order' && !$has_grn)
                                                    <!-- Notice the change here -->
                                                    <!-- Edit Purchase Order button -->
                                                    <a class="me-3"
                                                        href="{{ url('admin/purchase-order/edit/' . $po->id) }}">
                                                        <img src="/assets/admin/img/icons/edit.svg" alt="img">
                                                    </a>
                                                @endif
                                                @if ($check->slug === 'delete_purchase_order' && !$has_grn)
                                                    <!-- Notice the change here -->
                                                    <!-- Delete Purchase Order button -->
                                                    <a type="button" class="po_delete" data-id="{{ $po->id }}"
                                                        data-toggle="modal">
                                                        <img src="/assets/admin/img/icons/delete.svg" alt="img">
                                                    </a>
                                                @endif
                                            @endforeach
                                        @endif
                                        &nbsp;
                                        <a type="button"
                                            href="{{ url('admin/purchase-order/generate-pdf/' . $po->id) }}"
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
    $(".po_delete").click(function() {
        var purchase_order_id = $(this).data("id");
        //  alert(purchase_order_id);
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
                    url: "/admin/purchase-order/delete-purchase-order/" + purchase_order_id,
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
                        $(".po_delete").prop("disabled", false);
                        setTimeout(function() {
                            window.location.href =
                                "{{ url('admin/purchase-order') }}";
                        }, 1500);
                    },
                    error: function(e) {
                        // Handle error response
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.error(e);
                        $(".po_delete").prop("disabled", false);
                    }
                });
            }
        });
    });
</script>

{{-- po products view --}}
<script>
    $(".po_products").click(function() {
        var purchase_order_id = $(this).data("id");
        // alert(purchase_order_id);
        $.ajax({
            method: 'GET',
            dataType: "json",
            url: "/admin/purchase-order/po_products_view/" + purchase_order_id,
            cache: false,
            success: function(data) {
                let dataArray = data.data;
                console.log(dataArray);

                // Clear previous data in the modal body
                $('.details_body').empty();

                // Loop through the purchase order products and append rows to the modal table
                dataArray.forEach(function(product) {
                    var row = '<tr>' +
                        '<td>' + product.product_code + '</td>' +
                        '<td>' + product.product_name + '</td>' +
                        '<td class="text-center">' + product.quantity + '</td>' +
                        // '<td class="text-center">' + product.grn_quantity + '</td>' +
                        // '<td class="text-center">' + product.pending_quantity + '</td>' +
                        '</tr>';
                    $('.details_body').append(row);
                });

                // // Show the modal
                // $('#proViewModal').modal('show');
            },
            error: function(e) {
                console.log(e);
            },
        });
    });
</script>

</body>

</html>
