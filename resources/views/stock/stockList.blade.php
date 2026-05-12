@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Stock List</h4>
                <h6>Manage your Stock</h6>
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
                    
                    
                     <div class="wordset">
                        <ul>
                            <li>
                                <a type="button" href="{{ url('admin/stock/generate-pdf') }}" class="pdf_products">
                                    <img src="/assets/admin/img/icons/pdf.svg" alt="img">
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table datanew container">
                    <thead>
                        <tr>
                            <th style="display: none">
                                <label class="checkboxs">
                                    <input type="checkbox" id="select-all">
                                    <span class="checkmarks"></span>
                                </label>
                            </th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th class="text-center">Unit Of Measurment</th>
                            <th class="text-center">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stocks as $stock)
                            <tr>
                                <td style="display: none">
                                    <label class="checkboxs">
                                        <input type="checkbox" class="select-single">
                                        <span class="checkmarks"></span>
                                    </label>
                                </td>
                                <td>{{ $stock->product_code }}</td>
                                <td>{{ $stock->product_name }}</td>
                                <td class="text-center">{{ $stock->unit_of_measurement }}</td>
                                <td class="text-center">{{ $stock->stock_quantity }}</td>


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




</body>

</html>
