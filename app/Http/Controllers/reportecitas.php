<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
class reportecitas extends Controller
{
   public function reportecitasExport(){
  // return "Hola Reporte Triples";
    return view('livewire.reporte.citas.reportecitas');
   }
   


}
