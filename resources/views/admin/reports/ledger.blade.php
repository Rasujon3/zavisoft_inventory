@extends('admin_master')
@section('content')
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Ledger - {{ $account->name }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/sales')}}">Sales</a></li>
                        <li class="breadcrumb-item active">Ledger - {{ $account->name }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
        <section class="content">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Ledger - {{ $account->name }}</h3>
                </div>
                <form method="GET">
                    From: <input type="date" name="from" value="{{ $from }}">
                    To: <input type="date" name="to" value="{{ $to }}">
                    <button type="submit">Filter</button>
                </form>

                <hr>

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Debit</th>
                        <th>Credit</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $running = 0; @endphp
                    @foreach($lines as $line)

                        @php
                            $running += $line->debit;
                            $running -= $line->credit;
                        @endphp

                        <tr>
                            <td>{{ $line->journalEntry->entry_date }}</td>
                            <td>{{ $line->journalEntry->description }}</td>
                            <td>{{ number_format($line->debit,2) }}</td>
                            <td>{{ number_format($line->credit,2) }}</td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>

                <h5>Current Balance: {{ number_format($running,2) }}</h5>
            </div>
        </section>
    </div>
@endsection
