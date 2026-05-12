@include('layouts.header')
<div class="page-wrapper">
    <div class="content">

        <div class="row">
            @if (isset($getLoginUserPermission))

                @foreach ($getLoginUserPermission as $check)
                    @if ($check->slug === 'purchase_order_list')
                        <div class="col-lg-3 col-sm-6 col-12">
                            <a href="{{ route('purchase-orders') }}">
                                <div class="dash-count">
                                    <div class="dash-counts">
                                        <h4>{{ $purchaseOrderCount }}</h4>
                                        <h5>Purchase Order List</h5>
                                    </div>
                                    <div class="dash-imgs">
                                        <i data-feather="file-text"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @elseif ($check->slug === 'grn_list')
                        <div class="col-lg-3 col-sm-6 col-12 ">
                            <a href="{{ route('get-all-grns') }}">
                                <div class="dash-count das1">
                                    <div class="dash-counts">
                                        <h4>{{ $grnCount }}</h4>
                                        <h5>Grn List</h5>
                                    </div>
                                    <div class="dash-imgs">
                                        <i data-feather="folder"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @elseif ($check->slug === 'stock')
                        <div class="col-lg-3 col-sm-6 col-12 ">
                            <a href="{{ route('stock-get-all') }}">
                                <div class="dash-count das2">
                                    <div class="dash-counts">
                                        <h4>{{ $stockCount }}</h4>
                                        <h5>Products</h5>
                                    </div>
                                    <div class="dash-imgs">
                                        <i data-feather="package"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @elseif ($check->slug === 'issue_note_list')
                        <div class="col-lg-3 col-sm-6 col-12 ">
                            <a href="{{ route('get-all-issue-note') }}">
                                <div class="dash-count das3">
                                    <div class="dash-counts">
                                        <h4>{{ $issueNoteCount }}</h4>
                                        <h5>Issue List</h5>
                                    </div>
                                    <div class="dash-imgs">
                                        <i data-feather="list"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
