<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class aplication extends Model
{
    use HasFactory,SoftDeletes;
    // protected $guarded = ['id'];
    protected $fillable = [

        'id',
        'applicant_type',
        'appicant_name',
        'applicant_father_name',
        'appicant_sumiti_name',
        'applicant_sumiti_registration_no',
        'applicant_p_m_name',
        'gostir_name',
        'applicant_g_p_m_name',
        'district',
        'upozila',
        'union',
        'post',
        'wordNo',
        'village',
        'mobile_number',
        'nid_no',
        'nolkup_type',
        'nolkup_size',
        'area_description',
        'area_name',
        'mouja_name',
        'JL_No',
        'khotiyan_no',
        'dag_NO',
        'land_amount',
        'near_nolkup_type',
        'near_nolkup_uttor',
        'near_nolkup_dokkhin',
        'near_nolkup_purbo',
        'near_nolkup_poscim',
        'electricity_distance',
        'description',
        'deposite_fee',
        'deposite_date',
        'owner_type',
        'passport_size_mage',
        'nid_copy',
        'land_copy',
        'khotiyan_copy',
        'tax_copy',
        'map',
        'wyarisan',
        'status',
        'approved_date',
    ];
}
