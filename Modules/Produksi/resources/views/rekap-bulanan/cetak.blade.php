<!DOCTYPE html> {{-- halaman cetak berdiri sendiri (tanpa layout/sidebar), ukuran A4 landscape --}}
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $no_dokumen }} - Rekap Produksi Bulanan {{ $judul_bulan }}</title> {{-- judul ini juga jadi nama file saat "Save as PDF" --}}
  <style>
    @page { size: A4 landscape; margin: 12mm 12mm 14mm 12mm; } /* ukuran kertas & margin saat dicetak (atas kanan bawah kiri) */
    * { box-sizing: border-box; } /* lebar elemen sudah termasuk padding */
    body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #000; margin: 0; background: #e9ecef; } /* huruf standar laporan; latar abu-abu hanya di layar */
    .kertas { background: #fff; width: 297mm; min-height: 210mm; margin: 12px auto; padding: 12mm; box-shadow: 0 2px 8px rgba(0,0,0,.15); } /* tampilan "kertas" di layar */

    .kop { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 10px; } /* kop: kiri nama instansi, kanan nomor dokumen; garis ganda di bawah */
    .kop .instansi { font-size: 13pt; font-weight: bold; letter-spacing: .5px; } /* nama departemen */
    .kop .sub { font-size: 9.5pt; } /* nama divisi / aplikasi */
    .kop .dok { text-align: right; font-size: 8.5pt; line-height: 1.5; } /* kotak info dokumen */

    .judul { text-align: center; margin: 4px 0 12px; } /* judul laporan di tengah */
    .judul h1 { font-size: 13pt; margin: 0; text-decoration: underline; letter-spacing: 1px; } /* judul besar bergaris bawah */
    .judul div { font-size: 10pt; margin-top: 3px; } /* periode */

    table.data { width: 100%; border-collapse: collapse; } /* tabel utama; collapse = garis sel tidak dobel */
    table.data th, table.data td { border: 1px solid #000; padding: 2.5px 4px; } /* garis hitam tiap sel */
    table.data th { background: #d9d9d9; text-align: center; font-weight: bold; vertical-align: middle; } /* kepala tabel abu-abu */
    table.data td { text-align: right; } /* angka rata kanan */
    table.data td.tengah { text-align: center; } /* kolom tanggal/hari/shift rata tengah */
    table.data td.kiri { text-align: left; }
    table.data tr.minggu td { background: #f2f2f2; } /* hari Minggu diberi latar abu-abu muda */
    table.data tr.total td { background: #d9d9d9; font-weight: bold; } /* baris total */
    table.data tr.rata td { background: #efefef; font-style: italic; } /* baris rata-rata */
    thead { display: table-header-group; } /* kepala tabel diulang di setiap halaman cetak */
    tr { page-break-inside: avoid; } /* 1 baris tidak terpotong ke 2 halaman */

    .bawah { display: flex; gap: 16px; margin-top: 12px; page-break-inside: avoid; } /* bagian ringkasan + tanda tangan tidak terpotong */
    .ringkasan { flex: 1; } /* kolom kiri: ringkasan */
    .ringkasan table { border-collapse: collapse; width: 100%; } /* tabel kecil ringkasan */
    .ringkasan td, .ringkasan th { border: 1px solid #000; padding: 2px 5px; }
    .ringkasan th { background: #d9d9d9; text-align: left; }
    .catatan { font-size: 8pt; margin-top: 6px; line-height: 1.5; } /* keterangan kecil */

    .ttd { flex: 1.4; display: flex; justify-content: space-around; text-align: center; } /* kolom kanan: 3 kotak tanda tangan */
    .ttd .kotak { width: 31%; } /* 1 kotak tanda tangan */
    .ttd .ruang { height: 55px; } /* ruang kosong untuk tanda tangan basah */
    .ttd .nama { font-weight: bold; text-decoration: underline; } /* nama bergaris bawah */

    .kaki { margin-top: 10px; border-top: 1px solid #999; padding-top: 3px; font-size: 7.5pt; color: #444; display: flex; justify-content: space-between; } /* footer kecil */

    .alat { text-align: center; padding: 10px; } /* tombol di atas kertas (hanya layar) */
    .alat button { font-size: 10pt; padding: 6px 16px; margin: 0 4px; cursor: pointer; }

    @media print { /* aturan khusus saat dicetak */
      body { background: #fff; } /* tanpa latar abu-abu */
      .kertas { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; } /* ukuran mengikuti @page */
      .alat { display: none; } /* tombol tidak ikut tercetak */
      * { -webkit-print-color-adjust: exact; print-color-adjust: exact; } /* warna latar abu-abu tetap tercetak */
    }
  </style>
</head>
<body>
  @if (! $pratinjau) {{-- tombol hanya saat dibuka langsung (bukan di dalam pratinjau) --}}
    <div class="alat">
      <button onclick="window.print()">Cetak / Simpan PDF</button> {{-- window.print() = buka dialog cetak browser --}}
      <button onclick="window.close()">Tutup</button> {{-- tutup tab ini --}}
    </div>
  @endif

  <div class="kertas">
    <div class="kop"> {{-- ===== KOP LAPORAN ===== --}}
      <div>
        <div class="instansi">DEPARTEMEN DRAW TEXTURED YARN (DTY)</div>
        <div class="sub">Divisi Produksi &middot; {{ config('app.name') }}</div> {{-- &middot; = titik tengah --}}
      </div>
      <div class="dok">
        No. Dokumen : <b>{{ $no_dokumen }}</b><br>
        Periode : {{ $judul_bulan }}<br>
        Hari produksi : {{ $hari_produksi }} hari
      </div>
    </div>

    <div class="judul"> {{-- ===== JUDUL ===== --}}
      <h1>REKAP PRODUKSI BULANAN</h1>
      <div>Periode {{ $judul_bulan }}</div>
    </div>

    @php
      $format = ['jml_shift' => 0, 'troly' => 0, 'bobbin' => 0, 'berat' => 2, 'brk' => 0, 'problem' => 0, 'bleeding' => 0, 'suspend' => 0, 'waste' => 2, 'abnormal' => 0]; // kolom => jumlah angka di belakang koma
    @endphp

    <table class="data"> {{-- ===== TABEL PER TANGGAL ===== --}}
      <thead>
        <tr>
          <th rowspan="2" style="width: 4%;">Tgl</th> {{-- rowspan = sel setinggi 2 baris --}}
          <th rowspan="2" style="width: 7%;">Hari</th>
          <th rowspan="2" style="width: 5%;">Shift</th>
          <th colspan="3">Doffing</th> {{-- colspan = sel selebar 3 kolom --}}
          <th rowspan="2">Break<br>(putus)</th>
          <th rowspan="2">Problem<br>Mesin</th>
          <th rowspan="2">Bleeding<br>(menit)</th>
          <th rowspan="2">Suspend<br>(menit)</th>
          <th rowspan="2">Waste<br>(kg)</th>
          <th rowspan="2">Abnormal<br>(bobbin)</th>
          <th rowspan="2">Kehadiran<br>(hadir/total)</th>
        </tr>
        <tr>
          <th>Troly</th>
          <th>Bobbin</th>
          <th>Berat (kg)</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($baris as $b) {{-- ulangi setiap tanggal --}}
          <tr class="{{ $b['minggu'] ? 'minggu' : '' }}">
            <td class="tengah">{{ $b['tanggal'] }}</td>
            <td class="kiri">{{ $b['hari'] }}</td>
            @foreach ($format as $c => $desimal) {{-- ulangi setiap kolom angka --}}
              <td class="{{ $c == 'jml_shift' ? 'tengah' : '' }}">{{ $b['isi'][$c] !== null ? number_format($b['isi'][$c], $desimal, ',', '.') : '-' }}</td> {{-- tampilkan angka berformat Indonesia, '-' jika kosong --}}
            @endforeach
            <td class="tengah">{{ $b['isi']['total_operator'] !== null ? $b['isi']['hadir'].'/'.$b['isi']['total_operator'] : '-' }}</td> {{-- contoh 13/15 --}}
          </tr>
        @endforeach
        <tr class="total"> {{-- baris total sebulan --}}
          <td colspan="2" class="tengah">TOTAL</td>
          @foreach ($format as $c => $desimal)
            <td class="{{ $c == 'jml_shift' ? 'tengah' : '' }}">{{ number_format($total[$c], $desimal, ',', '.') }}</td>
          @endforeach
          <td class="tengah">{{ $total['hadir'] }}/{{ $total['total_operator'] }}</td>
        </tr>
        <tr class="rata"> {{-- baris rata-rata per hari produksi --}}
          <td colspan="2" class="tengah">Rata-rata/hari</td>
          @foreach ($format as $c => $desimal)
            <td class="{{ $c == 'jml_shift' ? 'tengah' : '' }}">{{ number_format($rata[$c], 2, ',', '.') }}</td> {{-- rata-rata selalu 2 angka di belakang koma --}}
          @endforeach
          <td class="tengah">{{ $total['total_operator'] ? number_format($total['hadir'] / $total['total_operator'] * 100, 1, ',', '.').'%' : '-' }}</td> {{-- persentase kehadiran --}}
        </tr>
      </tbody>
    </table>

    <div class="bawah"> {{-- ===== RINGKASAN + TANDA TANGAN ===== --}}
      <div class="ringkasan">
        <table>
          <tr><th colspan="2">Benang Abnormal per Jenis</th></tr>
          @forelse ($abnormal_jenis as $a) {{-- ulangi setiap jenis abnormal --}}
            <tr><td>{{ $a->nm_abnormal }}</td><td style="text-align: right; width: 30%;">{{ $a->jumlah }} bobbin</td></tr>
          @empty
            <tr><td colspan="2">Tidak ada temuan abnormal.</td></tr>
          @endforelse
        </table>
        <div class="catatan">
          @if ($waste_terbesar) {{-- jika ada data laporan --}}
            Waste terbesar: <b>{{ date('d-m-Y', strtotime($waste_terbesar->tgl_laporan)) }}</b> ({{ number_format($waste_terbesar->waste, 2, ',', '.') }} kg).<br>
          @endif
          Keterangan: "Shift" = jumlah shift yang sudah dilaporkan (maks. 3); "-" = tidak ada data.
        </div>
      </div>

      <div class="ttd">
        <div class="kotak">
          Dibuat oleh,
          <div class="ruang"></div> {{-- ruang tanda tangan --}}
          <div class="nama">{{ $pembuat->nama_karyawan ?? '....................' }}</div>
          <div>{{ $pembuat->nm_jabatan ?? '' }}</div>
        </div>
        <div class="kotak">
          Diperiksa oleh,
          <div class="ruang"></div>
          <div class="nama">{{ $kadiv ?? '....................' }}</div> {{-- titik-titik jika belum ada Kadiv Produksi di data karyawan --}}
          <div>Kadiv Produksi</div>
        </div>
        <div class="kotak">
          Disetujui oleh,
          <div class="ruang"></div>
          <div class="nama">{{ $hod ?? '....................' }}</div>
          <div>HOD DTY</div>
        </div>
      </div>
    </div>

    <div class="kaki"> {{-- ===== FOOTER ===== --}}
      <span>{{ $no_dokumen }} &middot; Rekap Produksi Bulanan {{ $judul_bulan }}</span>
      <span>Dicetak oleh {{ auth()->user()->username }} pada {{ date('d-m-Y H:i') }} WIB</span> {{-- waktu cetak --}}
    </div>
  </div>

  @if (! $pratinjau && request('langsung')) {{-- jika dibuka dari tombol Cetak, dialog print langsung muncul --}}
    <script>window.onload = function () { window.print(); };</script> {{-- tunggu halaman selesai dimuat, lalu buka dialog cetak --}}
  @endif
</body>
</html>
