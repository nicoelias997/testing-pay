<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Currency;
use App\Models\PaymentPlatform;
use App\Http\Requests\PaymentRequest;
use App\Services\PayPalService;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = Currency::all();
        $paymentMethods = PaymentPlatform::all();

        return Inertia::render('Ui/Payment/Index', [
            'currencies' => $currencies,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    //Like an approval operation
    public function create(Request $request)
    {
            
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {
        $request->validated();

        $paymentPlatform = PaymentPlatform::resolveService($request->payment_platform);
        
        return $paymentPlatform->handlePayment($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function approved(Request $request){
        $orderId = $request->get('token');

        if($orderId){
            $paymentPlatform = PaymentPlatform::resolveService($request->payment_platform);
            return $paymentPlatform->handleCaptureOrder($orderId);
        }
    }

    public function cancelled(){
       return redirect()->action([PaymentController::class, 'index']);
    }
}
