<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TGParametro extends Model
{
    protected $table = 't_g_parametros';

    protected $primaryKey = 'nu_id_parametro';

    protected $fillable = ['tx_nombre', 'tx_abreviatura', 'nu_item', 'tx_item_description', 'domain_id', 'nu_id_parametro','color'];
    
    public function domain()
    {
        return $this->belongsTo(Domains::class);
    }
}
