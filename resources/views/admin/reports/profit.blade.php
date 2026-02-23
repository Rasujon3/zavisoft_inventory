@extends('admin_master')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Profit Report</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{URL::to('/sales')}}">Sales</a></li>
                            <li class="breadcrumb-item active">Profit Report</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <section class="content">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Profit Report</h3>
                </div>
                <form method="GET">
                    From: <input type="date" name="from" value="{{ $from }}">
                    To: <input type="date" name="to" value="{{ $to }}">
                    <button type="submit">Filter</button>
                </form>

                <hr>

                <table class="table table-bordered">
                    <tr>
                        <th>Total Revenue</th>
                        <td>{{ number_format($revenue,2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Expense</th>
                        <td>{{ number_format($expense,2) }}</td>
                    </tr>
                    <tr>
                        <th>Net Profit</th>
                        <td>{{ number_format($profit,2) }}</td>
                    </tr>
                </table>
            </div>
        </section>

    </div>

@endsection
