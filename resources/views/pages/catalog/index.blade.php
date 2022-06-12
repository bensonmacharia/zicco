@extends('adminlte::page')

@section('title', 'Product')

@section('content_header')
    <h1>Product</h1>
@stop

@section('content')
<div class="row">
    @foreach($product as $row)
        <div class="col-lg-3 d-flex align-items-stretch">
            <div class="card mb-3">
                <img src="{{ asset('image_product/'.$row->image) }}" class="card-img-top" alt="No IMAGE">
                <div class="card-body">
                    <p class="text-center"><strong>{{ $row->name }}</strong></p>
                    <p class="text-center"><strong>{{ 'KES. '.number_format($row->price, 0, ',', '.') }}</strong></p>
                    <p class="card-text">{!! $row->description !!}</p>
                </div>
                <div class="card-footer">
                    <p class="text-center"><button class="btn btn-warning">More</button></p>
                </div>
            </div>
        </div>
    @endforeach
</div>
@stop

@section('css')
<style>
.card {
    border : 1px solid #000 !important;
}
</style>
@stop

@section('js')
@stop
