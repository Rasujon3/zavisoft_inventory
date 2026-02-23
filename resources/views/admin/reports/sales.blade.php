@extends('admin_master')
@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Sales Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sales Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Total : {{ number_format($totalSum,2) }} BDT</h3>
                </div>

                <div class="card-body">
                    <div class="card w-100">
                        <div class="card-header"><h5>Filter Sales Report</h5></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="date" class="form-control" id="from_date"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="date" class="form-control" id="to_date"/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btn-block filter-order">
                                        <i class="fa fa-search"></i> SEARCH
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fetch-data table-responsive">
                        <table id="sales-report" class="table table-bordered table-striped data-table">
                            <thead>
                            <tr>
                                <th>Total Sales</th>
                                <th>Total VAT</th>
                                <th>Total COGS</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function(){
            var salesTable = $('#sales-report').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('financial.reports.data') }}",
                    data: function (d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.search = $('.dataTables_filter input').val();
                    }
                },
                columns: [
                    {data: 'order_id', name: 'order_id'},
                    {data: 'order_date', name: 'order_date'},
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'customer_phone', name: 'customer_phone'},
                    {data: 'total', name: 'total'},
                    {data: 'status', name: 'status'},
                    {data: 'courier_status', name: 'courier_status'},
                ]
            });

            $('.filter-order').click(function(e){
                e.preventDefault();
                salesTable.draw();
            });
        });
    </script>
@endpush
