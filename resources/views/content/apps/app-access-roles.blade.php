@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Roles - Apps')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
	<div class="d-flex flex-column justify-content-center">
		<h4 class="mb-1 mt-3"><span class="text-muted fw-light">{{ __('Roles & Permissions') }}/</span> {{ __('Roles') }}</h4>
		<p class="text-muted">{{ __('Manage roles and their assignments') }}</p>
	</div>
</div>

<!-- Role cards -->
<div class="row g-4">
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h6 class="fw-normal mb-2">Total 4 users</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png') }}" alt="Avatar">
            </li>
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png') }}" alt="Avatar">
            </li>
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar">
            </li>
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/3.png') }}" alt="Avatar">
            </li>
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end mt-1">
          <div class="role-heading">
            <h4 class="mb-1">Administrator</h4>
            <a href="javascript:;" class="role-edit-modal"><span>Edit Role</span></a>
          </div>
          <a href="javascript:void(0);" class="text-muted"><i class="ti ti-copy ti-md"></i></a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h6 class="fw-normal mb-2">Total 7 users</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/4.png') }}" alt="Avatar">
            </li>
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar">
            </li>
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar">
            </li>
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end mt-1">
          <div class="role-heading">
            <h4 class="mb-1">Manager</h4>
            <a href="javascript:;" class="role-edit-modal"><span>Edit Role</span></a>
          </div>
          <a href="javascript:void(0);" class="text-muted"><i class="ti ti-copy ti-md"></i></a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h6 class="fw-normal mb-2">Total 5 users</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar">
            </li>
            <li class="avatar avatar-sm pull-up">
              <img class="rounded-circle" src="{{ asset('assets/img/avatars/9.png') }}" alt="Avatar">
            </li>
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end mt-1">
          <div class="role-heading">
            <h4 class="mb-1">Users</h4>
            <a href="javascript:;" class="role-edit-modal"><span>Edit Role</span></a>
          </div>
          <a href="javascript:void(0);" class="text-muted"><i class="ti ti-copy ti-md"></i></a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <h5 class="card-header">{{ __('Users by role') }}</h5>
      <div class="table-responsive">
        <table class="table border-top dataTable">
          <thead>
            <tr>
              <th>{{ __('User') }}</th>
              <th>{{ __('Role') }}</th>
              <th class="text-center">{{ __('Status') }}</th>
              <th class="text-center">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>john@example.com</td>
              <td>Administrator</td>
              <td class="text-center"><span class="badge bg-label-success">Active</span></td>
              <td class="text-center"><a href="javascript:;" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a><a href="javascript:;" class="text-danger"><i class="ti ti-trash ti-sm"></i></a></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!--/ Role cards -->
@endsection


