<?php

namespace App\Http\Controllers;

use App\Models\CitizenInformation;
use Illuminate\Http\Request;

class CitizenInformationController extends Controller
{


    public function citizeninformationNID(Request $request)
    {
        // return $request->all();

        $nationalIdNumber = $request->nidNumber;
        $dateOfBirth = date('Y-m-d',strtotime($request->dateOfBirth));




        $idcheck = CitizenInformation::where(['nationalIdNumber'=>$nationalIdNumber,'dateOfBirth'=>$dateOfBirth])->count();

        if($idcheck>0){
          $informations = CitizenInformation::where(['nationalIdNumber'=>$nationalIdNumber,'dateOfBirth'=>$dateOfBirth])->first();







          $responseData = [
            'informations'=>$informations,
            'type'=>'NID',
            'status'=>'found',
          ];
          return $responseData;
        }else{


            $idcheckForNid = CitizenInformation::where(['nationalIdNumber'=>$nationalIdNumber])->count();
            if($idcheckForNid>0){
                $responseData = [
                    'informations'=>[],
                    'type'=>'NID',
                    'status'=>'invaild dateOfBirth',
                  ];
                  return $responseData;
            }




            $requestBody = '{
                "nidNumber": "'.$nationalIdNumber.'",
                "dateOfBirth": "'.$dateOfBirth.'",
                "englishTranslation": true
              }';
              $curl = curl_init();

              curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://api.porichoybd.com/sandbox-api/v2/verifications/autofill',
                CURLOPT_URL => 'https://api.porichoybd.com/api/v2/verifications/autofill',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$requestBody,
                CURLOPT_HTTPHEADER => array(
                  'Content-Type: application/vnd.api+json',
                  'x-api-key: c4cc8c32-161c-496c-adfb-16eeed4607ad'
                ),
              ));

              $response = curl_exec($curl);

              curl_close($curl);

          return  $response = json_decode($response);

            if($response->status=='NO'){
                $responseData = [
                    'informations'=>[],
                    'type'=>'NID',
                    'status'=>'not-found',
                  ];
                  return $responseData;
            }elseif($response->status=='YES'){
                $NidInfo = (array)$response->data->nid;
                $NidInfo['dateOfBirth'] = $dateOfBirth;

                $presentAddressBNArray =  explode(", ",$response->data->presentAddressBN);
                $presentAddressBNArrayCount = count($presentAddressBNArray);
                if($presentAddressBNArrayCount>5){
                 $presentHoldingArray = explode(':',$presentAddressBNArray[0]);
                $NidInfo['presentHolding'] = $presentHoldingArray[1];

                 $presentVillageArray = explode(':',$presentAddressBNArray[1]);
                $NidInfo['presentVillage'] = $presentVillageArray[1];

                $NidInfo['presentUnion'] = $presentAddressBNArray[2];

                $presentPostArray = explode(':',$presentAddressBNArray[3]);
               $presentPostArray = explode('-',$presentPostArray[1]);


                $NidInfo['presentPost'] = ltrim($presentPostArray[0]);
                $NidInfo['presentPostCode'] = $presentPostArray[1];

                $NidInfo['presentThana'] = $presentAddressBNArray[4];

                $NidInfo['presentThana'] = $presentAddressBNArray[4];
                $NidInfo['presentDistrict'] = $presentAddressBNArray[5];
                }

                $permanentAddressArray =  explode(", ",$response->data->permanentAddressBN);
                $permanentAddressArrayCount = count($permanentAddressArray);
                if($permanentAddressArrayCount>5){
                 $permanentHoldingArray = explode(':',$permanentAddressArray[0]);
                $NidInfo['permanentHolding'] = $permanentHoldingArray[1];

                 $permanentVillageArray = explode(':',$permanentAddressArray[1]);
                $NidInfo['permanentVillage'] = $permanentVillageArray[1];

                $NidInfo['permanentUnion'] = $permanentAddressArray[2];

                $permanentPostArray = explode(':',$permanentAddressArray[3]);
                $permanentPostArray = explode('-',$permanentPostArray[1]);
                $NidInfo['permanentPost'] = ltrim($permanentPostArray[0]);
                $NidInfo['permanentPostCode'] = $permanentPostArray[1];

                $NidInfo['permanentThana'] = $permanentAddressArray[4];
                $NidInfo['permanentDistrict'] = $permanentAddressArray[5];
                }




                CitizenInformation::create($NidInfo);
            }



        }

        $informations =   CitizenInformation::where(['nationalIdNumber'=>$nationalIdNumber,'dateOfBirth'=>$dateOfBirth])->first();
        $responseData = [
            'informations'=>$informations,
            'type'=>'NID',
            'status'=>'found',
          ];
          return $responseData;

    }


    public function citizeninformationBRN(Request $request)
    {
        // return $request->all();

        $birthRegistrationNumber = $request->nidNumber;
        $dateOfBirth = date('Y-m-d',strtotime($request->dateOfBirth));
        $idcheck = CitizenInformation::where(['birthRegistrationNumber'=>$birthRegistrationNumber,'dateOfBirth'=>$dateOfBirth])->count();
        if($idcheck>0){
          return  CitizenInformation::where(['birthRegistrationNumber'=>$birthRegistrationNumber,'dateOfBirth'=>$dateOfBirth])->first();
        }else{
            $requestBody = '{
                "birthRegistrationNumber": "'.$birthRegistrationNumber.'",
                "dateOfBirth": "'.$dateOfBirth.'"
              }';

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.porichoybd.com/api/v1/verifications/autofill',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$requestBody,
                CURLOPT_HTTPHEADER => array(
                    'x-api-key: c4cc8c32-161c-496c-adfb-16eeed4607ad',
                    'Content-Type: application/json'
                ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);


            $response = json_decode($response);

            $NidInfo = (array)$response->data->birthRegistration;
            $NidInfo['dateOfBirth'] = $dateOfBirth;
            $NidInfo['birthRegistrationNumber'] = $birthRegistrationNumber;
            CitizenInformation::create($NidInfo);
        }

        return  CitizenInformation::where(['birthRegistrationNumber'=>$birthRegistrationNumber,'dateOfBirth'=>$dateOfBirth])->first();


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
     * @param  \App\Models\CitizenInformation  $citizenInformation
     * @return \Illuminate\Http\Response
     */
    public function show(CitizenInformation $citizenInformation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CitizenInformation  $citizenInformation
     * @return \Illuminate\Http\Response
     */
    public function edit(CitizenInformation $citizenInformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CitizenInformation  $citizenInformation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CitizenInformation $citizenInformation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CitizenInformation  $citizenInformation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CitizenInformation $citizenInformation)
    {
        //
    }
}





// {
//     "transactionId": "ebaad045-5c92-4540-9aaa-e60ca44ff333-OyaAgI1",
//     "creditCost": 2,
//     "creditCurrent": 21,
//     "status": "YES",
//     "data": {
//         "birthRegistration": {
//             "fullNameBN": "মোঃ নিশাদ হোসাইন",
//             "fullNameEN": "Md. Nisad Hossain",
//             "gender": "M",
//             "dateOfBirth": "2001-08-25T00:00:00",
//             "birthPlaceBN": "বানেশ্বর পাড়া, ডাকঘর-টেপ্রীগঞ্জ-৫০২০ উপজেলা-দেবীগঞ্জ-জেলা-পঞ্চগড়।",
//             "mothersNameBN": "বানেছা বেগম",
//             "mothersNameEN": "Banesa Begum",
//             "mothersNationalityBN": "বাংলাদেশী",
//             "mothersNationalityEN": "Bangladeshi",
//             "fathersNameBN": "মোঃ জয়নাল আবেদীন",
//             "fathersNameEN": "Joynal",
//             "fathersNationalityBN": "বাংলাদেশী",
//             "fathersNationalityEN": "Bangladeshi"
//         }
//     },
//     "errors": []
// }
