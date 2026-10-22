<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Rekap Absensi QCT {{ $periode }}</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    h2, p { margin: 0 0 6px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #555; padding: 4px 6px; text-align: center; }
    th { background: #eee; }
    td.kiri { text-align: left; }
  </style>
</head>

<body>
  <h2>Rekap Absensi Divisi QCT</h2>
  <p>Periode: {{ \Carbon\Carbon::parse($periode.'-01')->translatedFormat('F Y') }}</p>
  <p>Diverifikasi oleh: {{ $verifikator }} ({{ $waktu->format('d-m-Y H:i') }})</p>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>ID Karyawan</th>
        <th>Nama</th>
        <th>Grup</th>
        <th>Hadir</th>
        <th>Sakit</th>
        <th>Ijin</th>
        <th>Alfa</th>
        <th>Cuti</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $d)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->id_karyawan }}</td>
          <td class="kiri">{{ $d->nama_karyawan }}</td>
          <td>{{ $d->grup }}</td>
          <td>{{ $d->hadir }}</td>
          <td>{{ $d->sakit }}</td>
          <td>{{ $d->ijin }}</td>
          <td>{{ $d->alfa }}</td>
          <td>{{ $d->cuti }}</td>
          <td>{{ $d->total }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
