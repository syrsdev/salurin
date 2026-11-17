<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class PeriodeController extends Controller {
 public function index(){return response()->json(['data'=>Periode::orderByDesc('tgl_mulai')->get()]);}
 public function store(Request $request){
  $data=$request->validate(['nama_periode'=>'required|string|max:150','tgl_mulai'=>'required|date','tgl_selesai'=>'required|date|after_or_equal:tgl_mulai']);
  $periode=DB::transaction(function()use($data){$p=Periode::create($data);AuditTrail::catat('periode',$p->id,'create',null,$p->toArray());return $p;});
  return response()->json(['message'=>'Periode berhasil ditambahkan.','data'=>$periode],201);
 }
 public function update(Request $request,Periode $periode){
  $data=$request->validate(['nama_periode'=>'required|string|max:150','tgl_mulai'=>'required|date','tgl_selesai'=>'required|date|after_or_equal:tgl_mulai']);
  $p=DB::transaction(function()use($periode,$data){$old=$periode->toArray();$periode->update($data);AuditTrail::catat('periode',$periode->id,'update',$old,$periode->fresh()->toArray());return $periode->fresh();});
  return response()->json(['message'=>'Periode berhasil diperbarui.','data'=>$p]);
 }
 public function destroy(Periode $periode){
  if(DB::table('penyaluran')->where('id_periode',$periode->id)->exists())throw ValidationException::withMessages(['periode'=>'Periode sudah digunakan dalam penyaluran dan tidak bisa dihapus.']);
  DB::transaction(function()use($periode){$old=$periode->toArray();AuditTrail::catat('periode',$periode->id,'delete',$old,null);$periode->delete();});
  return response()->json(['message'=>'Periode berhasil dihapus.']);
 }
}
