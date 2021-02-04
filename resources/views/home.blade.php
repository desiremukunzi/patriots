@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Names</th>
                                <th>Tel</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Done At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$payment->full_names}}</td>
                                    <td>{{$payment->telephone}}</td>
                                    <td>{{$payment->fee_type}}</td>
                                    <td>{{$payment->amount}}</td>
                                    <td>{{$payment->created_at}}</td>
                                </tr>
                                @empty <tr><td colspan="6"></td><center>No  data</center></tr>
                                    
                          @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                      {!! $payments->links() !!}
                  </div>  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
