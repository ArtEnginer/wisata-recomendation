let table = {};

$(document).ready(function () {
  // Ambil data wisata & kriteria
  $(".modal").modal();
  Promise.all([
    cloud.add(baseUrl + "/api/wisata", {
      name: "wisata",
    }),
    cloud.add(baseUrl + "/api/kriteria-klasterisasi", {
      name: "kriteria-klasterisasi",
    }),
    cloud.add(baseUrl + "/api/nilai-kriteria-klasterisasi", {
      name: "nilai-kriteria-klasterisasi",
      callback: () => {
        if (table.nilai_kriteria_klasterisasi) {
          table.nilai_kriteria_klasterisasi.ajax.reload();
        }
      },
    }),
  ]).then(([wisata, kriteria]) => {
    // Siapkan kolom dinamis untuk DataTable
    let columns = [
      {
        title: "#",
        data: null,
        render: (d, t, r, m) => m.row + 1,
      },
      {
        title: "Wisata",
        data: "nama",
      },
      {
        title: "Kode",
        data: "kode",
      },
    ];

    kriteria.forEach((k) => {
      columns.push({
        title: k.nama,
        data: `nilai.${k.kode}`,
        defaultContent: "0",
      });
    });

    columns.push({
      title: "Aksi",
      data: "kode",
      render: (kode) => `
        <a role="button" class="btn btn-small orange btn-popup" data-target="edit" data-action="edit" data-id="${kode}">
          <i class="material-icons">edit</i>
        </a>`,
    });

    // Inisialisasi DataTable
    table.nilai_kriteria_klasterisasi = $(
      "#table-nilai-kriteria-klasterisasi"
    ).DataTable({
      responsive: true,
      ajax: {
        url: baseUrl + "/api/nilai-kriteria-klasterisasi/grouped",
        dataSrc: "",
      },
      columns: columns,
    });

    $(".preloader").slideUp();
  });
});

// Tampilkan form tambah
$("body").on("click", '[data-target="add"]', function (e) {
  e.preventDefault();

  const wisata = cloud.get("wisata") || [];
  const kriteria = cloud.get("kriteria-klasterisasi") || [];

  // Header
  let headerRow = `<th>WISATA</th>`;
  kriteria.forEach((k) => {
    headerRow += `<th>${k.nama}</th>`;
  });
  $("#form-table-head").html(headerRow);

  // Body
  let bodyRows = "";
  wisata.forEach((w) => {
    bodyRows += `<tr>`;
    bodyRows += `<td>${w.nama}<input type="hidden" name="wisata_kode[]" value="${w.kode}"></td>`;
    kriteria.forEach((k) => {
      bodyRows += `
        <td>
          <input type="number" name="nilai[${w.kode}][${k.kode}]" min="0" max="100" class="validate" required>
        </td>`;
    });
    bodyRows += `</tr>`;
  });

  $("#form-table-body").html(bodyRows);
});

// Submit form tambah
$("#form-add").on("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  $.ajax({
    type: "POST",
    url: baseUrl + "/api/nilai-kriteria-klasterisasi",
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      M.toast({ html: "Berhasil disimpan!" });
      cloud.pull("nilai-kriteria-klasterisasi");
      cloud.pull("wisata");
      cloud.pull("kriteria-klasterisasi");
      table.nilai_kriteria_klasterisasi.ajax.reload();
      $(".popup[data-page='add']").removeClass("open");
    },
    error: function () {
      M.toast({ html: "Gagal menyimpan data." });
    },
  });
});

$("body").on("click", '[data-action="edit"]', function (e) {
  e.preventDefault();
  const wisataKode = $(this).data("id");
  const wisata = cloud.get("wisata").find((w) => w.kode === wisataKode);
  const kriteria = cloud.get("kriteria-klasterisasi");

  if (!wisata || !kriteria) {
    M.toast({ html: "Data tidak ditemukan!" });
    return;
  }

  $("#edit-wisata-kode").val(wisataKode);

  // Header
  let headerRow = `<th>Kriteria</th><th>Nilai</th>`;
  $("#form-edit-table-head").html(headerRow);

  // Mapping nilai berdasarkan kriteria_kode
  const nilaiMap = {};
  wisata.nilai_kriteria_klasterisasi.forEach((n) => {
    nilaiMap[n.kriteria_klasterisasi_kode] = n.nilai;
  });

  // Buat body-nya
  let body = "";
  kriteria.forEach((k) => {
    const nilai = nilaiMap[k.kode] || 0;
    body += `
      <tr>
        <td>${k.nama}</td>
        <td>
          <input type="number" name="nilai[${k.kode}]" value="${nilai}" min="0" max="100" required>
        </td>
      </tr>`;
  });

  $("#form-edit-table-body").html(body);
  $(".popup[data-page='edit']").addClass("open");
});

// Submit form edit
$("#form-edit").on("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  // console isi dari formData
  for (let pair of formData.entries()) {
    console.log(`${pair[0]}: ${pair[1]}`);
  }

  $.ajax({
    type: "POST",
    url: baseUrl + "/api/nilai-kriteria-klasterisasi/update",
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      M.toast({ html: "Berhasil diperbarui!" });
      cloud.pull("nilai-kriteria-klasterisasi");
      cloud.pull("wisata");
      cloud.pull("kriteria-klasterisasi");
      table.nilai_kriteria_klasterisasi.ajax.reload();
      $(".popup[data-page='edit']").removeClass("open");
    },
    error: function () {
      M.toast({ html: "Gagal memperbarui data!" });
    },
  });
});
$("body").on("click", '[data-target="clustering"]', function () {
  $.ajax({
    url: baseUrl + "/api/wisata/clustering",
    method: "GET",
    success: function (res) {
      const { log, assignments, normalisasi } = res;

      let html = "";

      // Show normalization details
      html += `<h4>Normalisasi Data</h4>`;
      html += `<p>Normalisasi dilakukan dengan membagi setiap nilai dengan nilai maksimum per kriteria</p>`;
      html += `<table class="striped"><thead><tr><th>No</th><th>Kode</th><th>Nama</th><th>baseUrlal</th><th>Normalized</th></tr></thead><tbody>`;

      normalisasi.forEach((item) => {
        html += `<tr>
          <td>${item.no}</td>
          <td>${item.kode}</td>
          <td>${item.nama}</td>
          <td>[${item.baseUrlal.map((v) => v.toFixed(2)).join(", ")}]</td>
          <td>[${item.normalized.map((v) => v.toFixed(4)).join(", ")}]</td>
        </tr>`;
      });

      html += `</tbody></table><hr>`;

      // Show K-Means iterations
      html += `<h4>Proses Iterasi K-Means</h4>`;

      log.forEach((entry, iterIndex) => {
        html += `<div class="card-panel"><h5>Iterasi ${entry.iteration}</h5>`;

        // Show centroids
        html += `<h6>Centroids:</h6><ul>`;
        entry.centroids.forEach((c, i) => {
          html += `<li>Centroid ${i + 1}: [${c
            .map((v) => v.toFixed(4))
            .join(", ")}]</li>`;
        });
        html += `</ul>`;

        // Show distances and cluster assignments if available
        if (entry.distances) {
          html += `<h6>Perhitungan Jarak dan Penugasan Cluster:</h6>`;
          html += `<table class="striped"><thead><tr><th>Data</th>`;

          // Add centroid headers
          for (let i = 0; i < entry.centroids.length; i++) {
            html += `<th>Jarak ke C${i + 1}</th>`;
          }

          html += `<th>Cluster</th></tr></thead><tbody>`;

          // Add distance rows
          Object.entries(entry.distances).forEach(([dataIndex, distances]) => {
            html += `<tr><td>Data ${parseInt(dataIndex) + 1}</td>`;

            // Add distances to each centroid
            for (let i = 0; i < entry.centroids.length; i++) {
              const distKey = `C${i + 1}`;
              html += `<td>${distances[distKey].toFixed(4)}</td>`;
            }

            // Add assigned cluster
            html += `<td>Cluster ${entry.clusters[dataIndex] + 1}</td></tr>`;
          });

          html += `</tbody></table>`;
        }

        html += `</div><hr>`;
      });

      // Show final results
      html += `<h4>Hasil Akhir Clustering</h4>`;
      html += `<table class="striped"><thead><tr>
        <th>No</th>
        <th>Kode</th>
        <th>Nama</th>
        <th>Cluster</th>
        <th>Kategori</th>
        <th>Centroid</th>
      </tr></thead><tbody>`;

      assignments.forEach((a) => {
        const centroid = log[log.length - 1].centroids[a.cluster];
        html += `<tr>
          <td>${a.no}</td>
          <td>${a.kode}</td>
          <td>${a.nama}</td>
          <td>Cluster ${a.cluster + 1}</td>
          <td>${a.label}</td>
          <td>[${centroid.map((v) => v.toFixed(4)).join(", ")}]</td>
        </tr>`;
      });

      html += `</tbody></table>`;

      // Update modal content
      $("#clustering-log-content").html(html);
      const modal = M.Modal.getInstance($("#modal-clustering-log"));
      modal.open();

      M.toast({ html: "Clustering selesai!" });
      table.nilai_kriteria_klasterisasi.ajax.reload();
    },
    error: function () {
      M.toast({ html: "Gagal melakukan clustering!" });
    },
  });
});
