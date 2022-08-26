@extends('adminlte::page')

@section('title', 'Guide')

@section('content_header')
    <h1>User Guide</h1>
@stop

@section('content')
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row mt-3">
                <h5 class="pl-3">Add Product List</h5>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <div class="list-group" id="list-tab" role="tablist">
                        <a class="list-group-item list-group-item-action active" id="list-category-list" data-toggle="list" href="#list-category" role="tab" aria-controls="category">Step 1: Add Product Category</a>
                        <a class="list-group-item list-group-item-action" id="list-product-list" data-toggle="list" href="#list-product" role="tab" aria-controls="product">Step 2: Add Product</a>
                        <a class="list-group-item list-group-item-action" id="list-stock-list" data-toggle="list" href="#list-stock" role="tab" aria-controls="stock">Step 3: Add Stock</a>
                    </div>
                </div>
                <div class="col-8">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="list-category" role="tabpanel" aria-labelledby="list-category-list"><ul><li>From the left side menu, select Add Product to open the Manage categories page.</li><li>Press the Add Category button at the top and fill in the product category name e.g. Mouse, Adpaters, HDD Casings e.t.c</li><li>Press the Save button when done.</li></ul></div>
                        <div class="tab-pane fade" id="list-product" role="tabpanel" aria-labelledby="list-product-list"><ul><li>From the left side menu, select Add Product to open the Manage products page.</li><li>Press the Add Product button at the top and fill in the product details</li><li>The Avg. price is the selling price for each unit.</li><li>The product image upload filed is optional. Not a must you upload the image.</li><li>Press the Save button when done.</li></ul></div>
                        <div class="tab-pane fade" id="list-stock" role="tabpanel" aria-labelledby="list-stock-list"><ul><li>From the left side menu, select Add Stock to open the Add stock page.</li><li>Press the Add Stock button at the top and fill in the stock details</li><li>The Total Cost amount field should be filled with the total cost of ordering all the units including transport and clearance costs.</li><li>Press the Save button when done.</li></ul></div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <h5 class="pl-3">Record Sales</h5>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <div class="list-group" id="list-tab" role="tablist">
                        <a class="list-group-item list-group-item-action" id="list-sale-list" data-toggle="list" href="#list-sale" role="tab" aria-controls="sale">Step 1: Record Sale</a>
                        <a class="list-group-item list-group-item-action" id="list-customer-list" data-toggle="list" href="#list-customer" role="tab" aria-controls="customer">Step 2: Add Customer</a>
                    </div>
                </div>
                <div class="col-8">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade" id="list-sale" role="tabpanel" aria-labelledby="list-sale-list"><ul><li>From the left side menu, select Record Sale to open the Manage sales page.</li><li>Press the New Sale button at the top and fill in the sale details.</li><li>The Price amount should be the amount charged for each unit.</li><li>If the customer is not listed in the dropdown menu, add the user following the Add customer instructions (Step 2)</li><li>Amount paid field should contain the total amount the customer has paid for the products not including credit amount</li><li>Receipt No. and Invoice No. fileds are optional.</li><li>Press the Save button when done.</li></ul></div>
                        <div class="tab-pane fade" id="list-customer" role="tabpanel" aria-labelledby="list-customer-list"><ul><li>From the left side menu, select Customers to open the Manage customers page.</li><li>Press the Add button at the top and fill in the customer details</li><li>For the status field, select Active.</li><li>Press the Save button when done and the customer should now be visible when you are populating a sale.</li></ul></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .modal {
        overflow-y:auto;
    }
</style>
@stop

@section('js')
<script type="text/javascript">

$(document).ready(function(){

});
</script>
@stop
