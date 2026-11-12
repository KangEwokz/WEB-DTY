<tr> {{-- 1 baris = 1 bobbin abnormal; $i = nomor baris (atau '__I__' untuk cetakan JavaScript), $a = isi baris (array, kosong untuk baris baru) --}}
  <td class="nomor-baris"></td> {{-- nomor urut diisi JavaScript --}}
  <td>
    <select name="abnormal[{{ $i }}][kode_mesin]" class="form-control @error('abnormal.'.$i.'.kode_mesin') is-invalid @enderror">
      <option value="">- Mesin -</option>
      @foreach ($pilihan['mesin'] as $m)
        <option value="{{ $m->kode_mesin }}" {{ ($a['kode_mesin'] ?? '') == $m->kode_mesin ? 'selected' : '' }}>{{ $m->kode_mesin }} ({{ $m->line }}-{{ $m->posisi }})</option> {{-- tampil kode + lokasi --}}
      @endforeach
    </select>
  </td>
  <td>
    <select name="abnormal[{{ $i }}][merge]" class="form-control @error('abnormal.'.$i.'.merge') is-invalid @enderror">
      <option value="">- Merge -</option>
      @foreach ($pilihan['benang'] as $b)
        <option value="{{ $b->merge }}" {{ ($a['merge'] ?? '') == $b->merge ? 'selected' : '' }}>{{ $b->merge }}</option>
      @endforeach
    </select>
  </td>
  <td>
    <select name="abnormal[{{ $i }}][id_jns_abn]" class="form-control @error('abnormal.'.$i.'.id_jns_abn') is-invalid @enderror">
      <option value="">- Jenis -</option>
      @foreach ($pilihan['jenis'] as $j)
        <option value="{{ $j->id_jns_abn }}" {{ ($a['id_jns_abn'] ?? '') == $j->id_jns_abn ? 'selected' : '' }}>{{ $j->nm_abnormal }}</option>
      @endforeach
    </select>
  </td>
  <td>
    <select name="abnormal[{{ $i }}][kode_troly]" class="form-control @error('abnormal.'.$i.'.kode_troly') is-invalid @enderror">
      <option value="">- Troly -</option>
      @foreach ($pilihan['troly'] as $t) {{-- hanya troly berjenis Abnormal --}}
        <option value="{{ $t->kode_troly }}" {{ ($a['kode_troly'] ?? '') == $t->kode_troly ? 'selected' : '' }}>{{ $t->kode_troly }} ({{ $t->kapasitas }})</option> {{-- tampil kode + kapasitas --}}
      @endforeach
    </select>
  </td>
  <td>
    <input type="text" name="abnormal[{{ $i }}][keterangan]" value="{{ $a['keterangan'] ?? '' }}" maxlength="100" class="form-control" placeholder="opsional">
  </td>
  <td class="text-end">
    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus baris" onclick="hapusBaris(this)"><i class="ti-trash"></i></button> {{-- type="button" = tidak mengirim form; hapusBaris = fungsi JavaScript di _form --}}
  </td>
</tr>
