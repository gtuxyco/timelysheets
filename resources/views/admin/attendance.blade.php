@extends('layouts.default')

    @section('styles')
    <link href="{{ asset('/assets/vendor/mdtimepicker/mdtimepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/vendor/air-datepicker/dist/css/datepicker.min.css') }}" rel="stylesheet">
    <style>
        .ui.active.modal {position: relative !important;}
        .datepicker {z-index: 999 !important;}
        .datepickers-container {z-index: 9999 !important;}
    </style>
    @endsection

    @section('content')
    @include('admin.modals.modal-add-attendance')

    <div class="container-fluid">
        <div class="row">
            <h2 class="page-title">Attendance
                <a href="{{ url('clock') }}" class="ui positive button mini offsettop5 float-right"><i class="ui icon clock"></i>Clock In/Out</a>
                <button class="ui positive button mini offsettop5 btn-add float-right"><i class="ui icon plus"></i>Add</button>

            </h2>
        </div>  

        <div class="row">
            <div class="box box-success">
                <div class="box-body">
                    <table width="100%" class="table table-striped table-hover" id="dataTables-example" data-order='[[ 4, "asc" ]]'>
                        <thead>
                            <tr>
                                <th>ID #</th>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Total Hours</th>
                                <th>Status (In / Out)</th>
                                @isset($clock_comment)
                                    @if($clock_comment == 1)
                                        <th>Comment</th>
                                    @endif
                                @endisset
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($data)
                            @foreach ($data as $d)
                            <tr>
                                <td>{{ $d->idno }}</td>
                                <td>{{ $d->date }}</td>
                                <td>{{ $d->employee }}</td>
                                <td>@php $IN = date('h:i:s A', strtotime($d->timein)); echo $IN; @endphp</td>
                                <td>
                                    @isset($d->timeout)
                                        @php 
                                            $OUT = date('h:i:s A', strtotime($d->timeout));
                                        @endphp
                                        @if($d->timeout != NULL)
                                            {{ $OUT }}
                                        @endif
                                    @endisset
                                </td>
                                <td class="align-right">
                                    @isset($d->totalhours)
                                        @if($d->totalhours != null) 
                                            @php
                                                if(stripos($d->totalhours, ".") === false) {
                                                    $h = $d->totalhours;
                                                } else {
                                                    $HM = explode('.', $d->totalhours); 
                                                    $h = $HM[0]; 
                                                    $m = $HM[1];
                                                }
                                            @endphp
                                        @endif
                                        @if($d->totalhours != null)
                                            @if(stripos($d->totalhours, ".") === false) 
                                                {{ $h }} hr
                                            @else 
                                                {{ $h }} hr {{ $m }} minutes
                                            @endif
                                        @endif
                                    @endisset
                                </td>
                                <td>
                                    @if($d->status_timein != null OR $d->status_timeout != null) 
                                        <span class="@if($d->status_timein == 'Late Arrival') orange @else blue @endif">{{ $d->status_timein }}</span> / 
                                        
                                        @isset($d->status_timeout) 
                                            <span class="@if($d->status_timeout == 'Early Departure') red @else green @endif">
                                                {{ $d->status_timeout }}
                                            </span> 
                                        @endisset
                                    @else
                                        <span class="blue">{{ $d->status_timein }}</span>
                                    @endif 
                                </td>
                                @isset($clock_comment)
                                    @if($clock_comment == 1)
                                        <td>{{ $d->comment }}</td>
                                    @endif
                                @endisset
                                <td class="align-right">
                                    <a href="{{ url('/attendance/edit/'.$d->id) }}" class="ui circular basic icon button tiny"><i class="edit outline icon"></i></a>
                                    <a href="{{ url('/attendance/delete/'.$d->id) }}" class="ui circular basic icon button tiny"><i class="trash alternate outline icon"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    @endsection

    @section('scripts')
    <script src="{{ asset('/assets/vendor/air-datepicker/dist/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/air-datepicker/dist/js/i18n/datepicker.en.js') }}"></script>
    <script src="{{ asset('/assets/vendor/mdtimepicker/mdtimepicker.min.js') }}"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#dataTables-example').DataTable({responsive: true,pageLength: 15,lengthChange: false,searching: true,sorting: false,});
    });

    $('.jtimepicker').mdtimepicker({ format: 'h:mm:ss tt', hourPadding: true });
    $('.airdatepicker').datepicker({ language: 'en', dateFormat: 'yyyy-mm-dd' });

    $('.ui.dropdown.getid').dropdown({ onChange: function(value, text, $selectedItem) {
        $('select[name="employee"] option').each(function() {
            if($(this).val()==value) {var id = $(this).attr('data-id');$('input[name="id"]').val(id);};
        });
    }});

    </script>
    @endsection