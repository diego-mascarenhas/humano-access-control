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
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function ()
{
	$('.users-table').DataTable({
		processing: true,
		serverSide: false,
		ajax: '{{ route('app-access-roles.users-data') }}',
		columns: [
			{ data: null, title: '{{ __('User') }}', render: function (row)
				{
					return `<div class="d-flex align-items-center">
						<div class="d-flex flex-column">
							<span class="fw-medium">${row.name}</span>
							<small class="text-muted">${row.email}</small>
						</div>
					</div>`;
				}
			},
			{ data: 'role', title: '{{ __('Role') }}' },
			{ data: 'status', title: '{{ __('Status') }}', className: 'text-center', render: function (val)
				{
					const map = { 'Active': 'bg-label-success', 'Pending': 'bg-label-warning', 'Inactive': 'bg-label-secondary' };
					return `<span class="badge ${map[val] || 'bg-label-secondary'}">${val}</span>`;
				}
			}
		],
		drawCallback: function ()
		{
			$("#usersByRoleTable tbody tr").css({
				"user-select": "none",
				"-webkit-user-select": "none",
				"-moz-user-select": "none",
				"-ms-user-select": "none"
			});
		}
	});
});
</script>
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
  @foreach($roles as $role)
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h6 class="fw-normal mb-2">{{ __('Total') }} {{ $role['users_count'] }} {{ __('users') }}</h6>
        </div>
        <div class="d-flex justify-content-between align-items-end mt-1">
          <div class="role-heading">
            <h4 class="mb-1 text-capitalize">{{ $role['name'] }}</h4>
            <small class="text-muted">{{ __('Permissions') }}: {{ $role['permissions_count'] }}</small>
          </div>
          <a href="javascript:;" class="text-body role-edit-modal" data-role-id="{{ $role['id'] }}" data-role-name="{{ $role['name'] }}"><i class="ti ti-edit ti-md"></i></a>
        </div>
      </div>
    </div>
  </div>
  @endforeach

  <div class="col-12">
    <div class="card mt-4">
      <h5 class="card-header">{{ __('Users by role') }}</h5>
      <div class="card-datatable table-responsive">
        <table id="usersByRoleTable" class="table users-table"></table>
      </div>
    </div>
  </div>
</div>
<!--/ Role cards -->
@endsection

@push('modals')
<div class="modal fade" id="roleEditModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('Edit Role') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="roleEditForm" method="POST">
				@csrf
				<div class="modal-body">
					<p class="text-muted mb-2">{{ __('Set role permissions') }}</p>
					<div class="mb-3">
						<label class="form-label">{{ __('Role Name') }}</label>
						<input type="text" class="form-control" name="name" id="roleName" required>
					</div>
					<h6 class="mb-3">{{ __('Role Permissions') }}</h6>
					<div id="permissionsContainer" class="row g-3"></div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
					<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endpush

@push('scripts')
<script>
(function(){
	const modalEl = document.getElementById('roleEditModal');
	const modal = new bootstrap.Modal(modalEl);
	const form = document.getElementById('roleEditForm');
	const nameInput = document.getElementById('roleName');
	const container = document.getElementById('permissionsContainer');
	let currentRoleId = null;

	$(document).on('click', '.role-edit-modal', function ()
	{
		currentRoleId = this.getAttribute('data-role-id');
		const roleName = this.getAttribute('data-role-name') || '';
		nameInput.value = roleName;
		container.innerHTML = '<div class="text-muted">{{ __('Loading...') }}</div>';
		fetch(`{{ url('app/access-roles') }}/${currentRoleId}/permissions`)
			.then(r => r.json())
			.then(data => {
				nameInput.value = data.role.name;
				container.innerHTML = '';

				// Header row: Administrator Access + Select All
				const headerRow = document.createElement('div');
				headerRow.className = 'row g-0 align-items-center py-3 border-bottom';
				headerRow.innerHTML = `
					<div class="col-6 col-md-4 mb-2">
						<strong>{{ __('Administrator Access') }}</strong>
						<i class="ti ti-info-circle ms-1 text-muted"></i>
					</div>
					<div class="col-6 col-md-8">
						<label class="form-check mb-0">
							<input class="form-check-input" type="checkbox" id="selectAllPerms">
							<span class="form-check-label">{{ __('Select All') }}</span>
						</label>
					</div>
				`;
				container.appendChild(headerRow);

				data.modules.forEach((m, i) => {
					const row = document.createElement('div');
					row.className = 'col-12';
					row.innerHTML = `
						<div class="row g-0 align-items-center py-3 border-bottom">
							<div class="col-6 col-md-4 mb-2"><strong class="text-capitalize">${m.key}</strong></div>
							<div class="col-6 col-md-8">
								<div class="row g-0">
									<div class="col-4">
										<label class="form-check mb-0">
											<input class="form-check-input module-read" type="checkbox" name="modules[${i}][read]" value="1" ${m.readChecked ? 'checked' : ''} id="read_${i}">
											<span class="form-check-label">{{ __('Read') }}</span>
											${(m.readPerms||[]).map(p=>`<input type="hidden" name="modules[${i}][readPerms][]" value="${p}">`).join('')}
										</label>
									</div>
									<div class="col-4">
										<label class="form-check mb-0">
											<input class="form-check-input module-write" type="checkbox" name="modules[${i}][write]" value="1" ${m.writeChecked ? 'checked' : ''} id="write_${i}">
											<span class="form-check-label">{{ __('Write') }}</span>
											${(m.writePerms||[]).map(p=>`<input type="hidden" name="modules[${i}][writePerms][]" value="${p}">`).join('')}
										</label>
									</div>
									<div class="col-4">
										<label class="form-check mb-0">
											<input class="form-check-input module-create" type="checkbox" name="modules[${i}][create]" value="1" ${m.createChecked ? 'checked' : ''} id="create_${i}">
											<span class="form-check-label">{{ __('Create') }}</span>
											${(m.createPerms||[]).map(p=>`<input type="hidden" name="modules[${i}][createPerms][]" value="${p}">`).join('')}
										</label>
									</div>
								</div>
							</div>
						</div>
					`;
					container.appendChild(row);
				});

				// Select All toggles
				container.querySelector('#selectAllPerms')?.addEventListener('change', function(){
					const checked = this.checked;
					container.querySelectorAll('input.form-check-input').forEach(el => {
						if (el.id !== 'selectAllPerms') el.checked = checked;
					});
				});
				modal.show();
			});
	});

	form.addEventListener('submit', function (e)
	{
		e.preventDefault();
		const formData = new FormData(form);
		fetch(`{{ url('app/access-roles') }}/${currentRoleId}`, {
			method: 'POST',
			headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			body: formData
		}).then(r => r.json()).then(() => { modal.hide(); location.reload(); });
	});
})();
</script>
@endpush
