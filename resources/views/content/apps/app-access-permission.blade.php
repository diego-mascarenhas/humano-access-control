@extends('layouts/layoutMaster')

@section('title', 'Permission - Apps')

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
		<h4 class="mb-1 mt-3"><span class="text-muted fw-light">{{ __('Roles & Permissions') }}/</span> {{ __('Permissions') }}</h4>
		<p class="text-muted">{{ __('Manage application permissions and assignments') }}</p>
	</div>
</div>

<div class="card">
	<h5 class="card-header">{{ __('Permissions') }}</h5>
	<div class="table-responsive">
		<table class="table table-striped dataTable">
			<thead>
				<tr>
					<th>{{ __('Name') }}</th>
					<th class="text-center">{{ __('Assigned To') }}</th>
					<th class="text-center">{{ __('Created Date') }}</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><span class="fw-medium">view users</span></td>
					<td class="text-center"><span class="badge bg-label-primary">{{ __('Admin') }}</span></td>
					<td class="text-center">2024-01-15</td>
					<td class="text-center">
						<a href="javascript:;" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>
						<a href="javascript:;" class="text-danger"><i class="ti ti-trash ti-sm"></i></a>
					</td>
				</tr>
				<tr>
					<td><span class="fw-medium">edit projects</span></td>
					<td class="text-center"><span class="badge bg-label-info">{{ __('Editor') }}</span></td>
					<td class="text-center">2024-03-02</td>
					<td class="text-center">
						<a href="javascript:;" class="text-body"><i class="ti ti-edit ti-sm me-2"></i></a>
						<a href="javascript:;" class="text-danger"><i class="ti ti-trash ti-sm"></i></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
@endsection


