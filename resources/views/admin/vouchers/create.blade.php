@extends('layouts.admin')
@section('title','Voucher Baru')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Voucher Baru</h1>
@include('admin.vouchers._form', ['action' => route('admin.vouchers.store'), 'method' => 'POST'])
@endsection
