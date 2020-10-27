<div class="card">
<div class="card-body">
  <table class="table table-bordered">
	<tr><td>{{ _lang('Supplier Name') }}</td><td>{{ $supplier->supplier_name }}</td></tr>
	<tr><td>{{ _lang('Company Name') }}</td><td>{{ $supplier->company_name }}</td></tr>
	<tr><td>{{ _lang('Vat Number') }}</td><td>{{ $supplier->vat_number }}</td></tr>
	<tr><td>{{ _lang('Email') }}</td><td>{{ $supplier->email }}</td></tr>
	<tr><td>{{ _lang('Phone') }}</td><td>{{ $supplier->phone }}</td></tr>
	<tr><td>{{ _lang('Address') }}</td><td>{{ $supplier->address }}</td></tr>
	<tr><td>{{ _lang('Country') }}</td><td>{{ $supplier->country }}</td></tr>
	<tr><td>{{ _lang('City') }}</td><td>{{ $supplier->city }}</td></tr>
	<tr><td>{{ _lang('State') }}</td><td>{{ $supplier->state }}</td></tr>
	<tr><td>{{ _lang('Postal Code') }}</td><td>{{ $supplier->postal_code }}</td></tr>			
  </table>
</div>
</div>
