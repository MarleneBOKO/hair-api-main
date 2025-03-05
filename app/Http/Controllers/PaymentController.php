<?php

namespace App\Http\Controllers;

use App\Mail\RappelRendezVous;
use App\Models\Client;
use App\Models\Rendez_vou;
use App\Models\Transaction;
use App\Models\Type_coiffure;
use Exception;
use Kkiapay\Kkiapay;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function initiatePayment($transactionId ,$id_appointment)
    {

        $kkiapay = new Kkiapay(config('kkiapay.public_key'),  config('kkiapay.private_key') , config('kkiapay.secret'), $sandbox = true);
        $transactionkkp = $kkiapay->verifyTransaction($transactionId);

        if ($transactionkkp->status != "SUCCESS") {
            throw new Exception("La transaction a échoué. Veuillez réessayer.");
        }
        $rendezVous=Rendez_vou::where('id_appointment' , $id_appointment)->first();
        if ((double)$transactionkkp->amount != (double)$rendezVous->accompte) {
            return null;
        }


        $data = [];
        $data["performed_at"] =  $transactionkkp->performed_at;
        $data["id_transaction"] =  \Illuminate\Support\Str::uuid();
        $data["received_at"] =  $transactionkkp->received_at;
        $data["status"] =  $transactionkkp->status;
        $data["amount"] =  $transactionkkp->amount;
        $data["source"] =  $transactionkkp->source;
        $data["source_common_name"] =  $transactionkkp->source_common_name;
        $data["fees"] =  $transactionkkp->fees;
        $data["net"] =  $transactionkkp->net;
        $data["externalTransactionId"] = $transactionkkp->client->phone."_".$transactionkkp->transactionId;
        $data["acc_fullname"] =  $transactionkkp->client->fullname;
        $data["acc_phone"] =  $transactionkkp->client->phone;
        $data["acc_email"] =  $transactionkkp->client->email;
        $data["acc_person"] =  $transactionkkp->client->person;
        $data["transactionId"] =  $transactionkkp->transactionId;
        $data["transaction_object"] =  'buy-course';
        $data["appointment_id"] = $id_appointment;

        Transaction::create($data);
        $rendezVous->status="confirmed";
        $rendezVous->save();

    }



}

