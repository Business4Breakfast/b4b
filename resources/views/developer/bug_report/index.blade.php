@extends('layouts.app')

@section('title', '')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <div class="table-responsive">
                            <table class="table table-hover issue-tracker" id="item_table">
                                <thead>
                                    <th>Stav</th>
                                    <th>Popis</th>
                                    <th>Dokončené</th>
                                    <th>Meno</th>
                                    <th>Typ</th>
                                    <th>Dátum</th>
                                    <th></th>
                                </thead>
                                <tbody>
                                @if($items)
                                    @foreach($items as $i)
                                        <tr>
                                            <td>
                                                @if($i->status == 0)
                                                    <span class="label label-danger">{{__('constant.bug_report_status.0')}}</span>
                                                @elseif($i->status == 1)
                                                    <span class="label label-warning">{{__('constant.bug_report_status.1')}}</span>
                                                @elseif($i->status == 10)
                                                    <span class="label label-success">{{__('constant.bug_report_status.10')}}</span>
                                                @else
                                                    <span class="label label-warning">Chybmý status</span>
                                                @endif
                                            </td>
                                            <td class="issue-info">
                                                <a href="#">
                                                    ISSUE-{{$i->id}}
                                                </a>

                                                <small>
                                                    {{$i->description}}
                                                </small>
                                            </td>
                                            <td class="">
                                                <small>Dokončené: {{$i->progres}}%</small>
                                                <div class="progress progress-mini" style="height: 5px;">
                                                    <div style="width: {{$i->progres}}%;" class="progress-bar"></div>
                                                </div>
                                            </td>
                                            <td>
                                                {{$i->name}}  {{$i->surname}}
                                            </td>
                                            <td>
                                                @if($i->bug_type == 1)
                                                    <span class="text-danger"><i class="fa fa-bug"></i> {{__('constant.bug_report_type.1')}}</span>
                                                @elseif($i->bug_type == 2)
                                                    <span class="text-success"><i class="fa fa-lightbulb-o"></i> {{__('constant.bug_report_type.2')}}</span>
                                                @else
                                                    <span class="text-info"><i class="fa fa-info-circle"></i> {{__('constant.bug_report_type.3')}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                 {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $i->created_at)->format('d.m.y H:i') }}
                                            </td>
                                            <td class="text-right">
                                                <a href="{{route('developer.bug-report.show', $i->id)}}" class="btn btn-white btn-xs"> Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('css')

@endsection

@section('scripts')


    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>


    <script>

        $(document).ready(function(){

            // sorting s diakritikou
            $('#item_table').DataTable({
                "pageLength": 50,
                "dom": 'lrfrtip',
                columnDefs : [
                    { targets: 0, type: 'locale-compare' }
                ],
                "order": [0, 'asc']
            });

            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                "locale-compare-asc": function ( a, b ) {
                    return a.localeCompare(b, 'da', { sensitivity: 'accent' })
                },
                "locale-compare-desc": function ( a, b ) {
                    return b.localeCompare(a, 'da', { sensitivity: 'accent' })
                }
            });


        });

    </script>

@endsection