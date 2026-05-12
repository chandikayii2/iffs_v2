@include('layouts.header')
<div class="page-wrapper">
    <div class="content">
        <h2>Generate Product Report</h2>
        <form action="{{ route('reports.product.generate') }}" method="POST">
    @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="product_id" class="form-label">Product</label>
                    <select name="product_id" id="product_id" class="form-control select2" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->product_code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Date Range</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="date_range" id="this_week" value="this_week" checked>
                    <label class="form-check-label" for="this_week">This Week</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="date_range" id="last_week" value="last_week">
                    <label class="form-check-label" for="last_week">Last Week</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="date_range" id="custom" value="custom">
                    <label class="form-check-label" for="custom">Custom Range</label>
                </div>
            </div>
        </div>

        <div class="row mb-3" id="custom_date_range" style="display: none;">
            <div class="col-md-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Generate Report</button>
               <!-- <button type="submit" name="download" value="1" class="btn btn-success">Download PDF</button> -->
            </div>
        </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select a product",
            allowClear: true
        });

        // Date range toggle
        const customRadio = document.getElementById('custom');
        const customDateRange = document.getElementById('custom_date_range');
        
        document.querySelectorAll('input[name="date_range"]').forEach(radio => {
            radio.addEventListener('change', function() {
                customDateRange.style.display = this.value === 'custom' ? 'block' : 'none';
            });
        });
    });
</script>
