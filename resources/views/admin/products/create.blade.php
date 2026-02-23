@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Products</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/products')}}">All Products
                                </a></li>
                        <li class="breadcrumb-item active">Add Products</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Add Products</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('products.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Products Name <span class="required">*</span></label>
                                <input type="text" name="name" class="form-control" id="name"
                                    placeholder="Products Name" required="" value="{{old('name')}}">
                                @error('name')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku">SKU <span class="required">*</span></label>
                                <input type="text" name="sku" class="form-control" id="sku"
                                    placeholder="SKU" required="" value="{{old('sku')}}">
                                @error('sku')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="purchase_price">Purchase Price <span class="required">*</span></label>
                                <input type="text" name="purchase_price" class="form-control" id="purchase_price"
                                    placeholder="Purchase Price" required="" value="{{old('purchase_price')}}">
                                @error('purchase_price')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sell_price">Sell Price <span class="required">*</span></label>
                                <input type="text" name="sell_price" class="form-control" id="sell_price"
                                    placeholder="Sell Price" required="" value="{{old('sell_price')}}">
                                @error('sell_price')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opening_stock">Opening Stock <span class="required">*</span></label>
                                <input type="text" name="opening_stock" class="form-control" id="opening_stock"
                                    placeholder="Opening Stock" required="" value="{{old('opening_stock')}}">
                                @error('opening_stock')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group w-100 px-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </form>
        </div>
    </section>
</div>

@endsection

@push('scripts')

  <script src="{{asset('custom/multiple_files.js')}}"></script>

  <script>

  </script>

@endpush
