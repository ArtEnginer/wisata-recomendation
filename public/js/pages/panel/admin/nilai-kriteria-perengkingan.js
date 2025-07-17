let table = {};

$(document).ready(function () {
  // Ambil data wisata & kriteria
  $(".modal").modal();
  Promise.all([
    cloud.add(origin + "/api/wisata", {
      name: "wisata",
    }),
    cloud.add(origin + "/api/kriteria-perengkingan", {
      name: "kriteria-perengkingan",
    }),
    cloud.add(origin + "/api/nilai-kriteria-perengkingan", {
      name: "nilai-kriteria-perengkingan",
      callback: () => {
        if (table.nilai_kriteria_perengkingan) {
          table.nilai_kriteria_perengkingan.ajax.reload();
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
    table.nilai_kriteria_perengkingan = $(
      "#table-nilai-kriteria-perengkingan"
    ).DataTable({
      responsive: true,
      ajax: {
        url: origin + "/api/nilai-kriteria-perengkingan/grouped",
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
  const kriteria = cloud.get("kriteria-perengkingan") || [];

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
    url: origin + "/api/nilai-kriteria-perengkingan",
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      M.toast({ html: "Berhasil disimpan!" });
      cloud.pull("nilai-kriteria-perengkingan");
      cloud.pull("wisata");
      cloud.pull("kriteria-perengkingan");
      table.nilai_kriteria_perengkingan.ajax.reload();
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
  const kriteria = cloud.get("kriteria-perengkingan");

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
  wisata.nilai_kriteria_perengkingan.forEach((n) => {
    nilaiMap[n.kriteria_perengkingan_kode] = n.nilai;
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
    url: origin + "/api/nilai-kriteria-perengkingan/update",
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      M.toast({ html: "Berhasil diperbarui!" });
      cloud.pull("nilai-kriteria-perengkingan");
      cloud.pull("wisata");
      cloud.pull("kriteria-perengkingan");
      table.nilai_kriteria_perengkingan.ajax.reload();
      $(".popup[data-page='edit']").removeClass("open");
    },
    error: function () {
      M.toast({ html: "Gagal memperbarui data!" });
    },
  });
});
