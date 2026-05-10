<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CleanHtmlEntitiesCatPlantas extends Migration
{
    public function up()
    {
        $columns = ['Txt_Nombre_Planta', 'Txt_Codigo_Cliente', 'Txt_Sitio'];

        $plantas = DB::table('Cat_Plantas')->get();

        foreach ($plantas as $planta) {
            $updated = [];
            foreach ($columns as $col) {
                $value = $planta->$col ?? '';
                $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if ($decoded !== $value) {
                    $updated[$col] = $decoded;
                }
            }
            if (!empty($updated)) {
                DB::table('Cat_Plantas')
                    ->where('Id_Planta', $planta->Id_Planta)
                    ->update($updated);
            }
        }
    }

    public function down()
    {
        // No reversible — los datos originales estaban corruptos
    }
}
