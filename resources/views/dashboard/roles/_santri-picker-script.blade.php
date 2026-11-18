{{-- Pencarian & penyaringan santri untuk komponen _santri-picker. --}}
<script>
    (function () {
        document.querySelectorAll('[data-santri-picker]').forEach(function (picker) {
            var select = picker.querySelector('[data-santri-select]');
            var sumber = picker.querySelector('[data-santri-data]');
            var cari = picker.querySelector('[data-santri-search]');
            var kelas = picker.querySelector('[data-santri-kelas]');
            var info = picker.querySelector('[data-santri-info]');

            if (!select || !sumber) {
                return;
            }

            var semua = [];
            try {
                semua = JSON.parse(sumber.textContent || '[]');
            } catch (e) {
                semua = [];
            }

            var pilihanAwal = select.value;

            function render() {
                var q = (cari.value || '').trim().toLowerCase();
                var k = kelas.value || '';

                var hasil = semua.filter(function (s) {
                    var cocokKelas = !k || s.kelas === k;
                    var cocokCari = !q
                        || (s.nama || '').toLowerCase().indexOf(q) !== -1
                        || (s.induk || '').toLowerCase().indexOf(q) !== -1;

                    return cocokKelas && cocokCari;
                });

                var terpilih = select.value;
                select.innerHTML = '';

                var kosong = document.createElement('option');
                kosong.value = '';
                kosong.disabled = true;
                kosong.textContent = 'Pilih santri';
                select.appendChild(kosong);

                hasil.forEach(function (s) {
                    var opsi = document.createElement('option');
                    opsi.value = s.id;
                    opsi.textContent = s.nama + ' — Kelas ' + s.kelas + (s.induk ? ' (' + s.induk + ')' : '');
                    select.appendChild(opsi);
                });

                var masihAda = hasil.some(function (s) {
                    return String(s.id) === String(terpilih);
                });

                select.value = masihAda ? terpilih : '';
                if (!masihAda) {
                    kosong.selected = true;
                }

                if (info) {
                    info.textContent = hasil.length + ' dari ' + semua.length + ' santri';
                }
            }

            cari.addEventListener('input', render);
            kelas.addEventListener('change', render);

            // Kembalikan pilihan lama (mis. setelah validasi gagal), lalu saring.
            select.value = pilihanAwal;
            render();
        });
    })();
</script>
