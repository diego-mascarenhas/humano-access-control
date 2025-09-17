@extends('layouts/layoutMaster')

@section('title', 'Permission - Apps')

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
	$('.permissions-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ route('app-access-permission.data') }}',
		columns: [
			{ data: 'name', title: '{{ __('Nombre') }}', render: function (val)
				{ return `<span class=\"fw-medium\">${val}</span>`; }
			},
			{ data: 'assigned_to', title: '{{ __('Asignado a') }}', className: 'text-center', orderable: false, searchable: false },
			{ data: 'created_at', title: '{{ __('Fecha de creación') }}', className: 'text-center' },
			{ data: 'actions', title: '{{ __('Acciones') }}', className: 'text-center', orderable: false, searchable: false }
		],
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
		}
	});
});
</script>
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
    <div class="card-datatable table-responsive">
        <table class="table permissions-table w-100"></table>
    </div>
</div>
@endsection


