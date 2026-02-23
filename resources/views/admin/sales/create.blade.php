@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Sales</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/sales')}}">All Sales
                                </a></li>
                        <li class="breadcrumb-item active">Add Sales</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Sales</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('sales.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card-body">

                    {{-- Invoice + Customer --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>Invoice No *</label>
                            <input type="text" name="invoice_no" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label>Customer *</label>
                            <select name="customer_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Sale Date *</label>
                            <input type="date" name="sale_date" class="form-control" required>
                        </div>
                    </div>

                    <hr>

                    {{-- Product Table --}}
                    <table class="table table-bordered" id="saleTable">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th width="120">Qty</th>
                            <th width="150">Unit Price</th>
                            <th width="150">Total</th>
                            <th width="50">X</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-control product">
                                    <option value="">Select</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}"
                                                data-price="{{ $item->sell_price }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control qty">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][unit_price]" class="form-control price">
                            </td>
                            <td>
                                <input type="text" class="form-control total" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger removeRow">X</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <button type="button" id="addRow" class="btn btn-info">Add Item</button>

                    <hr>

                    {{-- Summary --}}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label>Discount</label>
                            <input type="number" name="discount" class="form-control" value="0">
                        </div>

                        <div class="col-md-3">
                            <label>VAT %</label>
                            <input type="number" name="vat_percent" class="form-control" value="0">
                        </div>

                        <div class="col-md-3">
                            <label>Paid Amount</label>
                            <input type="number" name="paid_amount" class="form-control" value="0">
                        </div>
                    </div>

                    <br>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </section>
</div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            let rowIndex = 1;

            // Add New Row
            document.getElementById('addRow').addEventListener('click', function () {

                let tableBody = document.querySelector('#saleTable tbody');

                let newRow = `
            <tr>
                <td>
                    <select name="items[${rowIndex}][product_id]" class="form-control product">
                        <option value="">Select</option>
                        @foreach($items as $item)
                <option value="{{ $item->id }}"
                                    data-price="{{ $item->sell_price }}">
                                {{ $item->name }}
                </option>
@endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control qty" min="1">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${rowIndex}][unit_price]" class="form-control price" readonly>
                </td>
                <td>
                    <input type="text" class="form-control total" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger removeRow">X</button>
                </td>
            </tr>
        `;

                tableBody.insertAdjacentHTML('beforeend', newRow);
                rowIndex++;
            });

            // Remove Row (Event Delegation)
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow')) {
                    let row = e.target.closest('tr');
                    row.remove();
                }
            });

            // Auto Set Price when product selected
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('product')) {

                    let selectedOption = e.target.options[e.target.selectedIndex];
                    let price = selectedOption.getAttribute('data-price') ?? 0;

                    let row = e.target.closest('tr');
                    let priceInput = row.querySelector('.price');

                    priceInput.value = parseFloat(price).toFixed(2);

                    calculateRow(row);
                }
            });

            // Quantity input calculation
            document.addEventListener('input', function (e) {
                if (e.target.classList.contains('qty')) {

                    let row = e.target.closest('tr');
                    calculateRow(row);
                }
            });

            // Function to calculate total per row
            function calculateRow(row) {

                let qty = parseFloat(row.querySelector('.qty').value) || 0;
                let price = parseFloat(row.querySelector('.price').value) || 0;

                let total = qty * price;

                row.querySelector('.total').value = total.toFixed(2);
            }

        });
    </script>
@endpush
