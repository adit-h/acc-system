<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title"> @lang('report.trans_journal_title')</h4>
                    </div>
                </div>
                <div class="card-body justify-content-between">
                    {!! Form::open(['route' => ['report.trans.journal.filter'], 'method' => 'post']) !!}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group search-input">
                                <span class="input-group-text" id="search-input">
                                    <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3.09277 9.40421H20.9167" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M16.442 13.3097H16.4512" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M12.0045 13.3097H12.0137" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7.55818 13.3097H7.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M16.442 17.1962H16.4512" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M12.0045 17.1962H12.0137" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7.55818 17.1962H7.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M16.0433 2V5.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7.96515 2V5.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M16.2383 3.5791H7.77096C4.83427 3.5791 3 5.21504 3 8.22213V17.2718C3 20.3261 4.83427 21.9999 7.77096 21.9999H16.229C19.175 21.9999 21 20.3545 21 17.3474V8.22213C21.0092 5.21504 19.1842 3.5791 16.2383 3.5791Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <input type="text" class="form-control vanila-datepicker" name="date_input" placeholder="Tanggal" value="{{ !empty($date) ? $date : '' }}">
                                <button class="btn btn-primary btn-sm" type="submit" id="date-filter">Filter</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a class="btn btn-outline-success" href="{{ route('report.trans.journal.export.excel', ['date_input' => !empty($date) ? $date : '']) }}">
                                    <span class="btn-inner">
                                        <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M14.7379 2.76175H8.08493C6.00493 2.75375 4.29993 4.41175 4.25093 6.49075V17.2037C4.20493 19.3167 5.87993 21.0677 7.99293 21.1147C8.02393 21.1147 8.05393 21.1157 8.08493 21.1147H16.0739C18.1679 21.0297 19.8179 19.2997 19.8029 17.2037V8.03775L14.7379 2.76175Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M14.4751 2.75V5.659C14.4751 7.079 15.6231 8.23 17.0431 8.234H19.7981" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M14.2882 15.3584H8.88818" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M12.2432 11.606H8.88721" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    Excel
                                </a>
                                <a class="btn btn-outline-success" href="{{ route('report.trans.journal.export.pdf', ['date_input' => !empty($date) ? $date : '']) }}">
                                    <span class="btn-inner">
                                        <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.7161 16.2234H8.49609" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M15.7161 12.0369H8.49609" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M11.2521 7.86011H8.49707" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M15.909 2.74976C15.909 2.74976 8.23198 2.75376 8.21998 2.75376C5.45998 2.77076 3.75098 4.58676 3.75098 7.35676V16.5528C3.75098 19.3368 5.47298 21.1598 8.25698 21.1598C8.25698 21.1598 15.933 21.1568 15.946 21.1568C18.706 21.1398 20.416 19.3228 20.416 16.5528V7.35676C20.416 4.57276 18.693 2.74976 15.909 2.74976Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    PDF
                                </a>
                                <a class="btn btn-outline-info" href="{{ route('report.trans.journal.export.html', ['date_input' => !empty($date) ? $date : '']) }}">
                                    <span class="btn-inner">
                                    <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M14.7379 2.76175H8.08493C6.00493 2.75375 4.29993 4.41175 4.25093 6.49075V17.2037C4.20493 19.3167 5.87993 21.0677 7.99293 21.1147C8.02393 21.1147 8.05393 21.1157 8.08493 21.1147H16.0739C18.1679 21.0297 19.8179 19.2997 19.8029 17.2037V8.03775L14.7379 2.76175Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M14.4751 2.75V5.659C14.4751 7.079 15.6231 8.23 17.0431 8.234H19.7981" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M14.2882 15.3584H8.88818" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M12.2432 11.606H8.88721" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    Print
                                </a>
                            </div>
                        </div>
                        <div class="col-md-5">
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        @if (!empty($filter))
                        <table id="basic-table" class="table table-bordered table-hover mb-0" role="grid">
                            <thead>
                                <tr>
                                    <th colspan="8">{{ $filter }}</th>
                                </tr>
                                <tr class="table-primary">
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Name</th>
                                    <th>Debet</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                    <th>No. Reff</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if (!empty($trans) && count($trans) > 0)
                                @foreach ($trans as $t)
                                <tr>
                                    <td>{{ date('d-M-Y', strtotime($t->trans_date)) }}</td>
                                    <td>{{ $t->fromCode }}</td>
                                    <td>{{ $t->fromName }}</td>
                                    <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                                    <td></td>
                                    <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                                    <td>{{ $t->reference }}</td>
                                    <td>{{ $t->description }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>{{ $t->toCode }}</td>
                                    <td>{{ $t->toName }}</td>
                                    <td></td>
                                    <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                                    <td>-</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforeach

                                {{--
                                @foreach ($trans_out as $t)
                                <tr>
                                    <td>{{ date('d-M-Y', strtotime($t->trans_date)) }}</td>
                                    <td>{{ $t->fromCode }}</td>
                                    <td>{{ $t->fromName }}</td>
                                    <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                                    <td></td>
                                    <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                                    <td>{{ $t->reference }}</td>
                                    <td>{{ $t->description }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>{{ $t->toCode }}</td>
                                    <td>{{ $t->toName }}</td>
                                    <td></td>
                                    <td>{{ number_format($t->value, 0, ',', '.') }}</td>
                                    <td>-</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforeach
                                --}}
                            @else
                                <tr>
                                    <td colspan="8">No Data Available.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        @else
                        <table id="basic-table" class="table table-bordered mb-0" role="grid">
                            <thead>
                                <tr>
                                    <th colspan="2">No Data</th>
                                </tr>
                            </thead>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
