@extends('admin.layouts.master')

@section('content')

<div class="panel panel-white">
		<div class="panel-heading clearfix">
			<h4 class="panel-title">Appointment Schedule </h4>
		</div>
		<div class="panel-body">
 

@if ($appointmentschedule->count())
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('quickadmin::templates.templates-view_index-list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                <thead>
                    <tr>
                        <th>
                            {!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}
                        </th>
                        <th>Name</th>
<th>Email</th>
<th>Phone number</th>
<th>Day/time</th>

                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($appointmentschedule as $row)
                        <tr>
                            <td>
                                {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                            </td>
                            <td>{{ $row->name }}</td>
<td>{{ $row->email }}</td>
<td>{{ $row->phone_number }}</td>
<td>{{ $row->day_time }}</td>

                            <td>
                               {!! 
								link_to_route(config('quickadmin.route').'.appointmentschedule.edit', trans('View'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
								
								
								
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("quickadmin::templates.templates-view_index-are_you_sure")."');",  'route' => array(config('quickadmin.route').'.appointmentschedule.destroy', $row->id))) !!}
                                {!! Form::submit(trans('quickadmin::templates.templates-view_index-delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-danger" id="delete">
                        {{ trans('quickadmin::templates.templates-view_index-delete_checked') }}
                    </button>
                </div>
            </div>
            {!! Form::open(['route' => config('quickadmin.route').'.appointmentschedule.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
            {!! Form::close() !!}
        </div>
	</div>
@else
    {{ trans('quickadmin::templates.templates-view_index-no_entries_found') }}
@endif

@endsection

</div>
</div>

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#delete').click(function () {
                if (window.confirm('{{ trans('quickadmin::templates.templates-view_index-are_you_sure') }}')) {
                    var send = $('#send');
                    var mass = $('.mass').is(":checked");
                    if (mass == true) {
                        send.val('mass');
                    } else {
                        var toDelete = [];
                        $('.single').each(function () {
                            if ($(this).is(":checked")) {
                                toDelete.push($(this).data('id'));
                            }
                        });
                        send.val(JSON.stringify(toDelete));
                    }
                    $('#massDelete').submit();
                }
            });
        });
    </script>
@stop