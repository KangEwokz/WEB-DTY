<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Rekap Check QCT {{ $periode }}</title>
  <style>
    body { font-family: sans-serif; font-size: 11px; }
    h2, h3, p { margin: 0 0 6px; }
    h3 { margin-top: 14px; }
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { border: 1px solid #555; padding: 4px 6px; text-align: center; }
    th { background: #eee; }
    td.kiri { text-align: left; }
  </style>
</head>

<body>
  <h2>Rekap Check QCT Benang DTY</h2>
  <p>Periode: {{ \Carbon\Carbon::parse($periode.'-01')->translatedFormat('F Y') }}</p>
  <p>Diverifikasi dan diarsipkan oleh: {{ $verifikator }} ({{ $waktu->format('d-m-Y H:i') }})</p>

  <h3>Ringkasan</h3>
  <table>
    <tr>
      <th>Troly</th>
      <th>Bobbin</th>
      <th>Lolos</th>
      <th>Abnormal</th>
      <th>Visual NG</th>
      <th>Denier NG</th>
      <th>Packing</th>
      <th>Hold</th>
      <th>Sampel OK / Total</th>
    </tr>
    <tr>
      <td>{{ $ringkasan['troly'] }}</td>
      <td>{{ $ringkasan['bobbin'] }}</td>
      <td>{{ $ringkasan['lolos'] }}</td>
      <td>{{ $ringkasan['abnormal'] }}</td>
      <td>{{ $ringkasan['visual_ng'] }}</td>
      <td>{{ $ringkasan['denier_ng'] }}</td>
      <td>{{ $ringkasan['packing'] }}</td>
      <td>{{ $ringkasan['hold'] }}</td>
      <td>{{ $ringkasan['sampel_ok'] }} / {{ $ringkasan['sampel_total'] }}</td>
    </tr>
  </table>

  <h3>Per Tanggal dan Shift</h3>
  <table>
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Shift</th>
        <th>Troly</th>
        <th>Bobbin</th>
        <th>Abnormal</th>
        <th>Lolos</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($per_shift as $s)
        <tr>
          <td>{{ date('d-m-Y', strtotime($s->tgl_doffing)) }}</td>
          <td>{{ $s->shift }}</td>
          <td>{{ $s->jml_troly }}</td>
          <td>{{ $s->jml_bobbin }}</td>
          <td>{{ $s->jml_abnormal }}</td>
          <td>{{ $s->jml_bobbin - $s->jml_abnormal }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <h3>Abnormal per Jenis</h3>
  <table>
    <thead>
      <tr>
        <th>Jenis</th>
        <th>Jumlah</th>
        <th>Packing</th>
        <th>Cutting</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($abnormal_jenis as $a)
        <tr>
          <td class="kiri">{{ $a->nm_abnormal }}</td>
          <td>{{ $a->jumlah }}</td>
          <td>{{ $a->packing }}</td>
          <td>{{ $a->cutting }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4">Tidak ada bobbin abnormal.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <h3>Troly per Grade</h3>
  <table>
    <thead>
      <tr>
        <th>Grade</th>
        <th>Jumlah Troly</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($grade as $g)
        <tr>
          <td>{{ $g->id_grade }}</td>
          <td>{{ $g->jumlah }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="2">Belum ada grade.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>

</html>
