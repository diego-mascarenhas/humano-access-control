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
    const tableId = '#usersByRoleTable';
    const buildRoleFilter = (api) => {
        const container = document.querySelector('.user_role');
        if (!container) return;
        const select = document.createElement('select');
        select.className = 'form-select text-capitalize';
        select.innerHTML = `<option value=""> {{ __('Seleccionar rol') }} </option>`;
        container.innerHTML = '';
        container.appendChild(select);
        const roles = new Set(api.column(1).data().toArray());
        Array.from(roles).sort().forEach(r => {
            const opt = document.createElement('option');
            opt.value = r; opt.textContent = r; select.appendChild(opt);
        });
        select.addEventListener('change', function(){
            const val = $.fn.dataTable.util.escapeRegex(this.value);
            api.column(1).search(val ? '^' + val + '$' : '', true, false).draw();
        });
    };

    const usersTable = $.fn.DataTable.isDataTable(tableId)
        ? $(tableId).DataTable()
        : $('.users-table').DataTable({
		processing: true,
		serverSide: false,
		ajax: '{{ route('app-access-roles.users-data') }}',
        columns: [
            { data: null, title: '{{ __('Usuario') }}', render: function (row)
				{
					return `<div class="d-flex align-items-center">
						<div class="d-flex flex-column">
							<span class="fw-medium">${row.name}</span>
							<small class="text-muted">${row.email}</small>
						</div>
					</div>`;
				}
			},
            { data: 'role', title: '{{ __('Rol') }}' },
            { data: 'status', title: '{{ __('Estado') }}', className: 'text-center', render: function (val)
				{
                    const map = { 'Active': 'bg-label-success', 'Pending': 'bg-label-warning', 'Inactive': 'bg-label-secondary' };
					return `<span class="badge ${map[val] || 'bg-label-secondary'}">${val}</span>`;
				}
			}
		],
        dom: '<"row mx-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end align-items-center gap-2"f>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            sProcessing: 'Procesando...',
            sLengthMenu: 'Mostrar _MENU_',
            sZeroRecords: 'No se encontraron resultados',
            sEmptyTable: 'Sin datos disponibles',
            sInfo: 'Mostrando _START_ a _END_ de _TOTAL_',
            sInfoEmpty: 'Mostrando 0 a 0 de 0',
            sInfoFiltered: '(filtrado de _MAX_)',
            sSearch: 'Buscar',
            oPaginate: { sFirst: 'Primero', sLast: 'Último', sNext: 'Siguiente', sPrevious: 'Anterior' }
        },
        initComplete: function(){ buildRoleFilter(this.api()); }
    });
});
</script>
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
    <div class="d-flex flex-column justify-content-center">
        <h4 class="mb-1 mt-3"><span class="text-muted fw-light">{{ __('Roles & Permissions') }}/</span> {{ __('Roles') }}</h4>
        <p class="text-muted">{{ __('Gestiona los roles y sus asignaciones') }}</p>
    </div>
    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <div class="user_role w-px-220"></div>
        <button id="addRoleBtn" class="btn btn-primary">+ {{ __('Añadir rol') }}</button>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const addBtn = document.getElementById('addRoleBtn');
        if (addBtn)
        {
            addBtn.addEventListener('click', async function(){
                const modalEl = document.getElementById('roleEditModal');
                const modal = new bootstrap.Modal(modalEl);
                // reset form for create
                document.getElementById('modalRoleName').value = '';
                const titleEl = document.querySelector('.role-title');
                if (titleEl) { titleEl.textContent = '{{ __('Crear rol') }}'; }
                const container = document.getElementById('permissionsContainer');
                container.innerHTML = '<div class="text-muted">{{ __('Cargando...') }}</div>';
                const resp = await fetch(`{{ route('app-access-roles.permissions-template') }}`);
                const data = await resp.json();
                container.innerHTML = '';
                data.modules.forEach((m, i) => {
                    const row = document.createElement('div');
                    row.className = 'list-group-item px-3 py-2';
                    row.innerHTML = `
                        <div class="row g-0 align-items-center">
                            <div class="col-6 col-md-4"><strong class="text-capitalize">${m.key}</strong></div>
                            <div class="col-6 col-md-8">
                                <div class="row g-0 align-items-center">
                                    <div class="col-4">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="modules[${i}][read]" value="1" id="create_read_${i}">
                                            <span class="form-check-label">{{ __('Leer') }}</span>
                                            ${(m.readPerms||[]).map(p=>`<input type="hidden" name="modules[${i}][readPerms][]" value="${p}">`).join('')}
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="modules[${i}][write]" value="1" id="create_write_${i}">
                                            <span class="form-check-label">{{ __('Escribir') }}</span>
                                            ${(m.writePerms||[]).map(p=>`<input type="hidden" name="modules[${i}][writePerms][]" value="${p}">`).join('')}
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="modules[${i}][create]" value="1" id="create_create_${i}">
                                            <span class="form-check-label">{{ __('Crear') }}</span>
                                            ${(m.createPerms||[]).map(p=>`<input type="hidden" name="modules[${i}][createPerms][]" value="${p}">`).join('')}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    container.appendChild(row);
                });
                // Override submit to create
                const form = document.getElementById('roleEditForm');
                form.onsubmit = async function(e){
                    e.preventDefault();
                    const fd = new FormData(form);
                    const payload = Object.fromEntries(fd.entries());
                    const resp = await fetch(`{{ route('app-access-roles.store') }}`,{ method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}, body: JSON.stringify(payload)});
                    if (resp.ok){ modal.hide(); location.reload(); } else { alert('Error'); }
                };
                modal.show();
            });
        }
    });
    </script>
</div>

<!-- Role cards -->
<div class="row g-4">
  @foreach($roles as $role)
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
					<div class="d-flex justify-content-between">
          <h6 class="fw-normal mb-2">{{ __('Total') }} {{ $role['users_count'] }} {{ __('usuarios') }}</h6>
        </div>
        <div class="d-flex justify-content-between align-items-end mt-1">
          <div class="role-heading">
            <h4 class="mb-1 text-capitalize">{{ $role['name'] }}</h4>
            <small class="text-muted">{{ __('Permisos') }}: {{ $role['permissions_count'] }}</small>
          </div>
          <a href="javascript:;" class="text-body role-edit-modal" data-role-id="{{ $role['id'] }}" data-role-name="{{ $role['name'] }}"><i class="ti ti-edit ti-md"></i></a>
        </div>
      </div>
    </div>
  </div>
  @endforeach

  <div class="col-12">
    <div class="card mt-4">
      <div class="card-datatable table-responsive">
        <table id="usersByRoleTable" class="table users-table w-100"></table>
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
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="roleEditForm" method="POST">
				@csrf
				<div class="modal-body">
					<div class="text-center mb-4">
					<h3 class="role-title mb-2">{{ __('Editar rol') }}</h3>
					<p class="text-muted">{{ __('Configurar permisos del rol') }}</p>
					</div>
					<div class="col-12 mb-4 fv-plugins-icon-container">
						<label class="form-label" for="modalRoleName">{{ __('Nombre del rol') }}</label>
						<input type="text" id="modalRoleName" name="name" class="form-control" placeholder="{{ __('Ingresá un nombre de rol') }}" tabindex="-1" required>
						<div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
					</div>
					<!-- Permissions table will be injected below -->
					<div id="permissionsContainer" class="row g-3"></div>
				</div>
				<div class="col-12 text-center mt-4 mb-3">
					<button type="submit" class="btn btn-primary me-sm-3 me-1 waves-effect waves-light">{{ __('Guardar') }}</button>
					<button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">{{ __('Cancelar') }}</button>
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
	const nameInput = document.getElementById('modalRoleName');
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

				// Build table per original style
				const wrapper = document.createElement('div');
				wrapper.className = 'col-12';
					const title = document.createElement('h5');
					title.textContent = `{{ __('Permisos del rol') }}`;
				wrapper.appendChild(title);
				const tableWrap = document.createElement('div');
				tableWrap.className = 'table-responsive';
				const table = document.createElement('table');
				table.className = 'table table-flush-spacing';
				const tbody = document.createElement('tbody');

				// Header row: Administrator Access + Select All
				const trHeader = document.createElement('tr');
				trHeader.innerHTML = `
					<td class="text-nowrap fw-medium">
						{{ __('Acceso de administrador') }} <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="{{ __('Permite acceso completo al sistema') }}" title="{{ __('Permite acceso completo al sistema') }}"></i>
					</td>
					<td>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="selectAll">
							<label class="form-check-label" for="selectAll">{{ __('Seleccionar todo') }}</label>
						</div>
					</td>
				`;
				tbody.appendChild(trHeader);

				data.modules.forEach((m, i) => {
					const tr = document.createElement('tr');
					tr.innerHTML = `
						<td class="text-nowrap fw-medium"><span class="text-capitalize">${m.key}</span></td>
						<td>
							<div class="d-flex">
								<div class="form-check me-3 me-lg-5">
									<input class="form-check-input module-read" type="checkbox" name="modules[${i}][read]" value="1" ${m.readChecked ? 'checked' : ''} id="read_${i}">
									<label class="form-check-label" for="read_${i}">{{ __('Leer') }}</label>
									${(m.readPerms||[]).map(p=>`<input type="hidden" name="modules[${i}][readPerms][]" value="${p}">`).join('')}
								</div>
								<div class="form-check me-3 me-lg-5">
									<input class="form-check-input module-write" type="checkbox" name="modules[${i}][write]" value="1" ${m.writeChecked ? 'checked' : ''} id="write_${i}">
									<label class="form-check-label" for="write_${i}">{{ __('Escribir') }}</label>
									${(m.writePerms||[]).map(p=>`<input type="hidden" name="modules[${i}][writePerms][]" value="${p}">`).join('')}
								</div>
								<div class="form-check">
									<input class="form-check-input module-create" type="checkbox" name="modules[${i}][create]" value="1" ${m.createChecked ? 'checked' : ''} id="create_${i}">
									<label class="form-check-label" for="create_${i}">{{ __('Crear') }}</label>
									${(m.createPerms||[]).map(p=>`<input type="hidden" name="modules[${i}][createPerms][]" value="${p}">`).join('')}
								</div>
							</div>
						</td>
					`;
					tbody.appendChild(tr);
				});

				table.appendChild(tbody);
				tableWrap.appendChild(table);
				wrapper.appendChild(tableWrap);
				container.appendChild(wrapper);

				// Select All toggles
				container.querySelector('#selectAll')?.addEventListener('change', function(){
					const checked = this.checked;
					container.querySelectorAll('input.form-check-input').forEach(el => {
						if (el.id !== 'selectAll') el.checked = checked;
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
