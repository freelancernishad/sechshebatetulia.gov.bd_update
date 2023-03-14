<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\aplication;
use App\Models\Uniouninfo;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;

class PaymentController extends Controller
{


    public function ipn(Request $request)
    {
        $data = $request->all();
        Log::info(json_encode($data));
        $sonod = aplication::find($data['cust_info']['cust_id']);
        $trnx_id = $data['trnx_info']['mer_trnx_id'];
        $payment = payment::where('trxid', $trnx_id)->first();

        $sonod_type = $payment->sonod_type;

        $Insertdata = [];
        if ($data['msg_code'] == '1020') {
            $Insertdata = [
                'status' => 'Paid',
                'method' => $data['pi_det_info']['pi_name'],
            ];
            $updateData = ['status' => 'unknown'];
            if($sonod_type=='application'){
                $updateData = ['status' => 'pending'];
            }if($sonod_type=='license_fee'){
                $updateData = ['payment_status' => 'Paid'];
            }
            $sonod->update($updateData);
        } else {
            $updateData = ['status' => 'unknown'];
            if($sonod_type=='application'){
                $updateData = ['status' => 'Failed'];
            }if($sonod_type=='license_fee'){
                $updateData = ['payment_status' => 'Failed'];
            }
            $sonod->update($updateData);
            $Insertdata = ['status' => 'Failed',];
        }
        $Insertdata['ipnResponse'] = json_encode($data);
        return $payment->update($Insertdata);
    }

    public function export(Request $request)
    {










        //  $request->all();
        $union = $request->union;
        $sonod_type = $request->sonod_type;
        $from = $request->from;
        $to = $request->to;



        if($sonod_type && $from && $to){
            if($sonod_type=='all'){
            // return Payment::where(['status'=>'Paid'])->whereBetween('date', [$from, $to])->orderBy('id','desc')->get();
            $row = Payment::with(['sonod'])->where(['union'=>$union,'status'=>'Paid'])->whereBetween('date', [$from, $to])->orderBy('id','desc')->get();
            }else{
                 $row = Payment::with(['sonod'])->where(['union'=>$union,'sonod_type'=>$sonod_type,'status'=>'Paid'])->whereBetween('date', [$from, $to])->orderBy('id','desc')->get();
            }

        $uniouninfo = Uniouninfo::where(['short_name_e' => $union])->first();
        $pdf = LaravelMpdf::loadView('Export',compact('row','uniouninfo','sonod_type','from','to'));
        return $pdf->stream("hlsdfhlo.pdf");

        }

        if($sonod_type=='all'){
            $row = Payment::with(['sonod'])->where(['union'=>$union,'status'=>'Paid'])->orderBy('id','desc')->get();
        }
        $row = Payment::with(['sonod'])->where(['union'=>$union,'sonod_type'=>$sonod_type,'status'=>'Paid'])->orderBy('id','desc')->get();
        // return Excel::download($export, 'report.xlsx');

        $uniouninfo = Uniouninfo::where(['short_name_e' => $union])->first();
        $pdf = LaravelMpdf::loadView('Export',compact('row','uniouninfo','sonod_type','from','to'));
        return $pdf->stream("hlsdfhlo.pdf");



    }



    public function Search(Request $request)
    {
        // return $request->all();
        $sonod_type = $request->sonod_type;

        $from = $request->from;
        $to = $request->to;
        $union = $request->union;

        if($union){


            if($from && $to){
                if($sonod_type=='all'){


                    return Payment::where(['union'=>$union,'status'=>'Paid'])->whereBetween('date', [$from, $to])->orderBy('id','desc')->get();
                }
                return Payment::where(['union'=>$union,'sonod_type'=>$sonod_type,'status'=>'Paid'])->whereBetween('date', [$from, $to])->orderBy('id','desc')->get();

            }elseif($from){
                if($sonod_type=='all'){
                return Payment::where(['union'=>$union,'status'=>'Paid'])->where('date',$from)->orderBy('id','desc')->get();
                }
                return Payment::where(['union'=>$union,'sonod_type'=>$sonod_type,'status'=>'Paid'])->where('date',$from)->orderBy('id','desc')->get();

            }else{
                if($sonod_type=='all'){
                return Payment::where(['union'=>$union,'status'=>'Paid'])->orderBy('id','desc')->get();
                }
                return Payment::where(['union'=>$union,'sonod_type'=>$sonod_type,'status'=>'Paid'])->orderBy('id','desc')->get();

            }
        }else{

            if($from && $to){
                if($sonod_type=='all'){
                return Payment::where(['status'=>'Paid'])->whereBetween('date', [$from, $to])->orderBy('id','desc')->get();
                }
                return Payment::where(['sonod_type'=>$sonod_type,'status'=>'Paid'])->whereBetween('date', [$from, $to])->orderBy('id','desc')->get();

            }elseif($from){
                if($sonod_type=='all'){
                return Payment::where(['status'=>'Paid'])->where('date',$from)->orderBy('id','desc')->get();
                }
                return Payment::where(['sonod_type'=>$sonod_type,'status'=>'Paid'])->where('date',$from)->orderBy('id','desc')->get();

            }else{
                if($sonod_type=='all'){
                return Payment::where(['status'=>'Paid'])->orderBy('id','desc')->get();
                }
                return Payment::where(['sonod_type'=>$sonod_type,'status'=>'Paid'])->orderBy('id','desc')->get();

            }
        }

    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
