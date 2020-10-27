@extends('layouts.app')

@section('content')
<!--calendar css-->
<link href="{{ asset('public/backend/plugins/fullcalendar/packages/core/main.css') }}" rel="stylesheet" />
<link href="{{ asset('public/backend/plugins/fullcalendar/packages/daygrid/main.css') }}" rel="stylesheet" />
<link href="{{ asset('public/backend/plugins/fullcalendar/packages/bootstrap/main.css') }}" rel="stylesheet" />
<link href="{{ asset('public/backend/plugins/fullcalendar/packages/timegrid/main.css') }}" rel="stylesheet" />
<link href="{{ asset('public/backend/plugins/fullcalendar/packages/list/main.css') }}" rel="stylesheet" />

<div class="row">
	<div class="col-12">
	<div class="card">
	<span class="d-none panel-heading">{{ _lang('Expense Calendar') }}</span>

	<div class="card-body">
		<div id='expense_calendar'></div>
	</div>
  </div>
 </div>
</div>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/plugins/fullcalendar/packages/core/main.js') }}"></script>
<script src="{{ asset('public/backend/plugins/fullcalendar/packages/daygrid/main.js') }}"></script>
<script src="{{ asset('public/backend/plugins/fullcalendar/packages/timegrid/main.js') }}"></script>
<script src="{{ asset('public/backend/plugins/fullcalendar/packages/interaction/main.js') }}"></script>
<script src="{{ asset('public/backend/plugins/fullcalendar/packages/list/main.js') }}"></script>
<script>

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('expense_calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      plugins: [ 'interaction', 'dayGrid', 'timeGrid' ],

      header: {
        left: 'prev,next today',
        center: 'title',
		right: 'dayGridMonth, timeGridWeek, timeGridDay'
      },
      defaultView: 'dayGridMonth',
      //defaultDate: '2019-08-12',
      navLinks: true, 
      editable: true,
      eventLimit: true,
	  eventBackgroundColor: "#F44336",
	  eventBorderColor: "#F44336",
	  timeFormat: 'h:mm',
	  events: [
			@php $currency = currency(); @endphp
			@foreach($transactions as $trans)
				{
				  title: '{{ $trans->expense_type->name." - ".currency($trans->account->account_currency)." ".$trans->amount }}',
				  start: '{{ $trans->trans_date }}',
				  url: '{{ action("ExpenseController@show", $trans->id) }}'
				},
			@endforeach
	  ],
	  eventRender: function(info) {	
        $(info.el).addClass('ajax-modal');	  
        $(info.el).data("title","{{ _lang('View Income') }}");	  
		
		/*var dotEl = info.el.getElementsByClassName('fc-event-dot')[0];
        if (dotEl) {
		   if(info.event.extendedProps.status == 'pending'){
			  dotEl.style.backgroundColor = '#FF5B5C';
		   }else if(info.event.extendedProps.status == 'completed'){
			  dotEl.style.backgroundColor = '#5A8DEE'; 
		   }else if(info.event.extendedProps.status == 'cancelled'){
			  dotEl.style.backgroundColor = '#d63031';
		   }else if(info.event.extendedProps.status == 'confirmed'){
			  dotEl.style.backgroundColor = '#39DA8A'; 
		   }
        }*/
	  },
	  eventClick: function(info) {
		info.jsEvent.preventDefault();
	  }
    });

    calendar.render();
});

</script>	
@endsection


