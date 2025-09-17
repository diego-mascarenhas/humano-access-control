{{-- Action column template for DataTables --}}
<div class="d-flex justify-content-center align-items-center">
	<a href="javascript:;" class="text-body" data-id="{{ $permission->id }}">
		<i class="ti ti-edit ti-sm me-2"></i>
	</a>
	<a href="javascript:;" class="text-danger" data-id="{{ $permission->id }}">
		<i class="ti ti-trash ti-sm"></i>
	</a>
</div>


