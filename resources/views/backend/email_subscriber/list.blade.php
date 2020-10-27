@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<span class="panel-title d-none">{{ _lang('Email Subscribers') }}</span>
			<div class="card-body">
			  <div class="table-responsive">
				  <table class="table table-striped data-table">
					<thead>
						<tr>
							<th>{{ _lang('Subscribed Date') }}</th>
							<th>{{ _lang('Email') }}</th>
							<th>{{ _lang('IP') }}</th>
						</tr>
					</thead>
					<tbody>
					  @php $date_format = get_option('date_format'); @endphp
					  @php $time_format = get_option('time_format') == '24' ? 'H:i' : 'h:i A'; @endphp

					  @foreach($email_subscribers as $subscriber)
						<tr>
							<td>{{ date("$date_format $time_format",strtotime($subscriber->created_at)) }}</td>
							<td>{{ $subscriber->email }}</td>
							<td>{{ $subscriber->ip_address }}</td>
						</tr>
					  @endforeach
					</tbody>
				  </table>
			  </div>
		
			</div>
		</div>
	</div>
</div>

@endsection


