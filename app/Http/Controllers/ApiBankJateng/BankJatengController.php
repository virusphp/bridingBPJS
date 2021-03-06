<?php

namespace App\Http\Controllers\ApiBankJateng;
use App\Http\Controllers\Controller;
use App\Repository\Tagihan;
use App\Transform\TransformTagihanBayar;
use App\Validation\PostTagihanBayar;
use Illuminate\Http\Request;

class BankJatengController extends Controller
{
    protected $pasien;

    public function __construct()
    {
        $this->tagihan = new Tagihan;
        $this->transformbayar = new TransformTagihanBayar;
    }

    public function bayarTagihan(Request $r, PostTagihanBayar $valid)
    {        
        $validate = $valid->rules($r);

        if ($validate->fails()) {
            $message = $valid->messages($validate->errors());
            return response()->jsonApi('01', implode(",",$message));    
        }

        $bayartagihan = $this->tagihan->bayarTagihan($r); 
        if($bayartagihan['status']=='01'){
            $message = [
                "messageError" => "Data Tagihan Pasien tidak di temukan!"
            ];        
            return response()->jsonApi('01', $message["messageError"]);
        }else if($bayartagihan['status']=='02'){ 
            // $transform = $this->transformbayar->mapperTagihanBayarLunas($bayartagihan['data']);
            // return response()->jsonApi('02', "Data Tagihan Pasien Sudah Dibayar", $transform);
            $message = [
                "messageError" => "Data Tagihan Pasien Sudah Dibayar"
            ];        
            return response()->jsonApi('02', $message["messageError"]);
        }else if($bayartagihan['status']=='05'){
            $message = [
                "messageError" => "Error Exception atau data yang dikirim tidak sesuai "
            ];        
            return response()->jsonApi('05', $message["messageError"]);
        }else{  
            if (count($bayartagihan['data'])==0) {
                $message = [
                    "messageError" => "Error Exception"
                ];        
                return response()->jsonApi('05', $message["messageError"]);;
            }
            $transform = $this->transformbayar->mapperTagihanBayarInsert($bayartagihan['data']);
            return response()->jsonApi('04', "Tagihan Berhasil disimpan", $transform);
        }
        
    }
}