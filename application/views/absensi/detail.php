<div class="row mb-2">
    <h4 class="col-xs-12 col-sm-6 mt-0">Detail Absen</h4>
    <div class="col-xs-12 col-sm-6 ml-auto text-right">
        <form action="" method="get">
            <div class="row">
                <div class="col">
                    <select name="bulan" id="bulan" class="form-control">
                        <option value="" disabled selected>-- Pilih Bulan --</option>
                        <?php foreach ($all_bulan as $bn => $bt) : ?>
                            <option value="<?= $bn ?>" <?= ($bn == $bulan) ? 'selected' : '' ?>><?= $bt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col ">
                    <select name="tahun" id="tahun" class="form-control">
                        <option value="" disabled selected>-- Pilih Tahun</option>
                        <?php for ($i = date('Y'); $i >= (date('Y') - 5); $i--) : ?>
                            <option value="<?= $i ?>" <?= ($i == $tahun) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col ">
                    <button type="submit" class="btn btn-primary btn-fill btn-block">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <table class="table border-0">
                            <tr>
                                <th class="border-0 py-0">Nama</th>
                                <th class="border-0 py-0">:</th>
                                <th class="border-0 py-0"><?= $karyawan->nama ?></th>
                            </tr>
                            <tr>
                                <th class="border-0 py-0">Divisi</th>
                                <th class="border-0 py-0">:</th>
                                <th class="border-0 py-0"><?= $karyawan->nama_divisi ?></th>
                            </tr>
                        </table>
                    </div>
                    <div class="col-xs-12 col-sm-6 ml-auto text-right mb-2">
                        <div class="dropdown d-inline">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="droprop-action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-print"></i>
                                Export Laporan
                            </button>
                            <div class="dropdown-menu" aria-labelledby="droprop-action">
                                <a href="<?= base_url('absensi/export_pdf/' . $this->uri->segment(3) . "?bulan=$bulan&tahun=$tahun") ?>" class="dropdown-item" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                <a href="<?= base_url('absensi/export_excel/' . $this->uri->segment(3) . "?bulan=$bulan&tahun=$tahun") ?>" class="dropdown-item" target="_blank"><i class="fa fa-file-excel-o"></i> Excel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h4 class="card-title mb-4">Absen Bulan : <?= bulan($bulan) . ' ' . $tahun ?></h4>
                <table class="table table-striped table-bordered">
                    <thead>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                    </thead>
                    <tbody>
                        <?php if ($absen) : ?>
                            <?php foreach ($hari as $i => $h) : ?>
                                <?php
                                $absen_harian = array_search($h['tgl'], array_column($absen, 'tgl')) !== false ? $absen[array_search($h['tgl'], array_column($absen, 'tgl'))] : '';
                                ?>
                                <tr <?= (in_array($h['hari'], ['Sabtu', 'Minggu'])) ? 'class="bg-dark text-white"' : '' ?> <?= ($absen_harian == '') ? 'class="bg-danger text-white"' : '' ?>>
                                    <td><?= ($i + 1) ?></td>
                                    <td><?= $h['hari'] . ', ' . $h['tgl'] ?></td>
                                    <td><?= date('l', strtotime($h['tgl'])) ?></td>
                                    <?php
                                    $tgl = @$h['tgl'] ? $h['tgl'] : date('d-m-Y');
                                    ?>
                                    <td>
                                        <?php
                                        if (date('l', strtotime($h['tgl'])) == 'Sunday' || date('l', strtotime($h['tgl'])) == 'Saturday') {
                                            echo 'Libur Akhir Pekans';
                                        } else {
                                            if ($jam) {
                                                $status = ucfirst($status);
                                                $CI = &get_instance();
                                                $CI->load->model('Jam_model', 'jam');
                                                $jam_kerja = $CI->jam->db->where('keterangan', $status)->get('jam')->row();
                                        
                                                if ($status == 'Masuk' && $jam > $jam_kerja->finish) {
                                                    if ($raw) {
                                                        return [
                                                            'status' => 'telat',
                                                            'text' => $jam
                                                        ];
                                                    } else {
                                                        return '<span class="badge badge-danger">' . $jam . '</span>';
                                                    }
                                                } elseif ($status == 'Pulang' && $jam > $jam_kerja->finish) {
                                                    if ($raw) {
                                                        return [
                                                            'status' => 'lembur',
                                                            'text' => $jam
                                                        ];
                                                    } else {
                                                        return '<span class="badge badge-success">' . $jam . '</span>';
                                                    }
                                                } else {
                                                    if ($raw) {
                                                        return [
                                                            'status' => 'normal',
                                                            'text' => $jam
                                                        ];
                                                    } else {
                                                        return '<span class="badge badge-primary">' . $jam . '</span>';
                                                    }
                                                }
                                            } else {
                                                if ($raw) {
                                                    return [
                                                        'status' => 'normal',
                                                        'text' => 'Tidak Hadir'
                                                    ];
                                                }
                                                return 'Tidak Hadir';
                                            }
                                        }
                                        ?>

                                    </td>
                                    <!-- <td><?= is_weekend($h['tgl']) ? 'Libur Akhir Pekan' : check_jam(@$absen_harian['jam_masuk'], 'masuk') ?></td> -->
                                    <td><?= date('l', strtotime($tgl) == 'Sunday' || date('l', strtotime($tgl) == 'Saturday')) ? 'Libur Akhir Pekan' : check_jam(@$absen_harian['jam_pulang'], 'pulang') ?></td>
                                    <!-- <td><?= is_weekend($h['tgl']) ? 'Libur Akhir Pekan' : check_jam(@$absen_harian['jam_pulang'], 'pulang') ?></td> -->
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td class="bg-light" colspan="4">Tidak ada data absen</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>