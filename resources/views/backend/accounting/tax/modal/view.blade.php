<div class="panel panel-default">
<div class="panel-body">
  <table class="table table-bordered">
    <tr><td>{{ _lang('Tax Name') }}</td><td>{{ $tax->tax_name }}</td></tr>
    <tr><td>{{ _lang('Rate') }}</td><td>{{ $tax->rate }}</td></tr>
    <tr><td>{{ _lang('Type') }}</td><td>{{ ucwords($tax->type) }}</td></tr>
  </table>
</div>
</div>
