<?php
namespace App\Services;
use App\Models\Guru;
use Carbon\CarbonImmutable;
class RekapService {
    public function hitung(Guru $guru, int $bulan, int $tahun): array {
        $awal=CarbonImmutable::create($tahun,$bulan,1)->startOfDay();
        $akhir=$awal->endOfMonth();
        $batas=CarbonImmutable::today();
        $tanggalAkhir=$akhir->lessThan($batas)?$akhir:$batas->subDay(); // hari ini belum dikategorikan alpa
        $records=$guru->absensis()->whereBetween('tanggal',[$awal->toDateString(),$akhir->toDateString()])->get()->keyBy(fn($r)=>$r->tanggal->toDateString());
        $rekap=['hadir'=>0,'terlambat'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0,'hari_kerja'=>0];
        $hari=array_map('intval',explode(',',$guru->jadwal?->hari_kerja ?: '1,2,3,4,5'));
        if ($tanggalAkhir->greaterThanOrEqualTo($awal)) {
            for($d=$awal;$d->lessThanOrEqualTo($tanggalAkhir);$d=$d->addDay()) {
                if (!in_array($d->dayOfWeekIso,$hari,true)) continue;
                if ($guru->tanggal_mulai && $d->lessThan(CarbonImmutable::parse($guru->tanggal_mulai))) continue;
                $rekap['hari_kerja']++;
                $status=$records->get($d->toDateString())?->status ?? 'alpa';
                $rekap[$status]++;
            }
        }
        // Keputusan hadir pada hari ini langsung dihitung, tetapi hari ini yang belum absen tidak dianggap alpa.
        if($batas->betweenIncluded($awal,$akhir) && in_array($batas->dayOfWeekIso,$hari,true) && (!$guru->tanggal_mulai || $batas->greaterThanOrEqualTo(CarbonImmutable::parse($guru->tanggal_mulai)))) {
            $record=$records->get($batas->toDateString());
            if($record){$rekap['hari_kerja']++;$rekap[$record->status]++;}
        }
        return $rekap;
    }
}
