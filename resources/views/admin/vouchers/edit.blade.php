@extends('layouts.admin')
@section('title','Edit Voucher')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Voucher</h1>
@include('admin.vouchers._form', ['action' => route('admin.vouchers.update', $voucher), 'method' => 'PUT', 'voucher' => $voucher])
@endsection
