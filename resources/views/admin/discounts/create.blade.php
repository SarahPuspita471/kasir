@extends('layouts.admin')
@section('title','Scheme Baru')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Scheme Baru</h1>
@include('admin.discounts._form', ['action'=>route('admin.discounts.store'),'method'=>'POST'])
@endsection
