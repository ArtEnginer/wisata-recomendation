const table = {
  peminjaman: $("#table-peminjaman").DataTable({
    responsive: true,
    ajax: {
      url: origin + "/api/peminjaman",
      dataSrc: "",
    },
    order: [
      [2, "asc"],
      [0, "asc"],
    ],
    columns: [
      {
        title: "#",
        data: "id",
        render: function (data, type, row, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        },
      },
      { title: "Nama", data: "barang.nama" },
      { title: "Kode", data: "barang.kode" },
      { title: "Jumlah", data: "jumlah" },
      { title: "Diajukan Oleh", data: "user.name" },

      {
        title: "Status Approval",
        data: "status_approval",
        render: function (data, type, row) {
          let status = "";
          switch (data) {
            case "pending":
              status = '<span class="badge blue">Pending</span>';
              break;
            case "acc":
              status = '<span class="badge green">Diterima</span>';
              break;
            case "tolak":
              status = '<span class="badge red">Ditolak</span>';
              break;
            default:
              status = '<span class="badge grey">Unknown</span>';
          }
          return status;
        },
      },
      {
        title: "Status",
        data: "status",
        render: function (data, type, row) {
          let status = "";
          switch (data) {
            //  'pinjam', 'kembali', 'hilang', 'rusak'
            case "pinjam":
              status = '<span class="badge blue">Dipinjam</span>';
              break;
            case "kembali":
              status = '<span class="badge green">Kembali</span>';
              break;
            case "hilang":
              status = '<span class="badge red">Hilang</span>';
              break;
            case "rusak":
              status = '<span class="badge orange">Rusak</span>';
            default:
              status = '<span class="badge grey">Unknown</span>';
          }
          return status;
        },
      },
      {
        title: "Aksi",
        data: "id",
        render: (data, type, row) => {
          let buttons = `<div class="table-control">`;

          const isFinalStatus = ["kembali", "rusak", "hilang"].includes(
            row.status
          );

          if (row.status_approval === "pending" && !isFinalStatus) {
            buttons += `
              <a role="button" class="btn waves-effect waves-light btn-action green" data-action="acc" data-id="${data}">
                <i class="material-icons">check</i>
              </a>
              <a role="button" class="btn waves-effect waves-light btn-action orange darken-2" data-action="tolak" data-id="${data}">
                <i class="material-icons">close</i>
              </a>
            `;
          } else if (row.status_approval === "acc" && !isFinalStatus) {
            buttons += `
              <a role="button" class="btn waves-effect waves-light btn-action btn-popup orange darken-2" data-target="edit" data-action="edit" data-id="${data}">
                <i class="material-icons">assignment_return</i>
              </a>
            `;
          }

          // Tombol delete hanya muncul jika status belum final
          if (!isFinalStatus) {
            buttons += `
              <a role="button" class="btn waves-effect waves-light btn-action red" data-action="delete" data-id="${data}">
                <i class="material-icons">delete</i>
              </a>
            `;
          }

          buttons += `</div>`;
          return buttons;
        },
      },
    ],
  }),
};

$("form#form-add").on("submit", function (e) {
  e.preventDefault();
  const data = {};
  $(this)
    .serializeArray()
    .map(function (x) {
      data[x.name] = x.value;
    });

  const form = $(this)[0];
  const elements = form.elements;
  for (let i = 0, len = elements.length; i < len; ++i) {
    elements[i].readOnly = true;
  }

  $.ajax({
    type: "POST",
    url: origin + "/api/peminjaman",
    data: data,
    cache: false,
    success: (data) => {
      $(this)[0].reset();
      cloud.pull("peminjaman");
      if (data.messages) {
        $.each(data.messages, function (icon, text) {
          Toast.fire({
            icon: icon,
            title: text,
          });
        });
      }
    },
    complete: () => {
      for (let i = 0, len = elements.length; i < len; ++i) {
        elements[i].readOnly = false;
      }
    },
  });
});

$("body").on("click", ".btn-action", function (e) {
  e.preventDefault();
  const action = $(this).data("action");
  const id = $(this).data("id");
  switch (action) {
    case "delete":
      Swal.fire({
        title: "Apakah anda yakin ingin menghapus data ini ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "DELETE",
            url: origin + "/api/peminjaman/" + id,
            cache: false,
            success: (data) => {
              table.peminjaman.ajax.reload();
              if (data.messages) {
                $.each(data.messages, function (icon, text) {
                  Toast.fire({
                    icon: icon,
                    title: text,
                  });
                });
              }
            },
          });
        }
      });
      break;
    case "edit":
      let dataEdit = cloud.get("peminjaman").find((x) => x.id == id);
      $("form#form-edit")[0].reset();
      $("form#form-edit").find("input[name=id]").val(dataEdit.id);
      $.each(dataEdit, function (field, val) {
        $("form#form-edit").find(`[name=${field}]`).val(val);
      });
      M.updateTextFields();
      M.textareaAutoResize($("textarea"));
      M.FormSelect.init(document.querySelectorAll("select"));
      break;
    case "acc":
      Swal.fire({
        title: "Apakah anda yakin ingin menerima data ini ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Terima",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "POST",
            url: origin + "/api/peminjaman/acc/" + id,
            cache: false,
            success: (data) => {
              table.peminjaman.ajax.reload();
              if (data.messages) {
                $.each(data.messages, function (icon, text) {
                  Toast.fire({
                    icon: icon,
                    title: text,
                  });
                });
              }
            },
          });
        }
      });
      break;
    case "tolak":
      Swal.fire({
        title: "Apakah anda yakin ingin menolak data ini ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Tolak",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "POST",
            url: origin + "/api/peminjaman/tolak/" + id,
            cache: false,
            success: (data) => {
              table.peminjaman.ajax.reload();
              if (data.messages) {
                $.each(data.messages, function (icon, text) {
                  Toast.fire({
                    icon: icon,
                    title: text,
                  });
                });
              }
            },
          });
        }
      });
      break;

    default:
      break;
  }
});

$("form#form-edit").on("submit", function (e) {
  e.preventDefault();
  const data = {};
  $(this)
    .serializeArray()
    .map(function (x) {
      data[x.name] = x.value;
    });

  const form = $(this)[0];
  const elements = form.elements;
  for (let i = 0, len = elements.length; i < len; ++i) {
    elements[i].readOnly = true;
  }

  $.ajax({
    type: "POST",
    url: origin + "/api/peminjaman/" + data.id,
    data: data,
    cache: false,
    success: (data) => {
      $(this)[0].reset();
      cloud.pull("peminjaman");
      if (data.messages) {
        $.each(data.messages, function (icon, text) {
          Toast.fire({
            icon: icon,
            title: text,
          });
        });
      }
      $(this).closest(".popup").find(".btn-popup-close").trigger("click");
    },
    complete: () => {
      for (let i = 0, len = elements.length; i < len; ++i) {
        elements[i].readOnly = false;
      }
    },
  });
});

$("body").on("keyup", "#form-add input[name=nama]", function (e) {
  $("#form-add input[name=name]").val($(this).val());
});
$("body").on("keyup", "#form-edit input[name=nama]", function (e) {
  $("#form-edit input[name=name]").val($(this).val());
});

function loadBarangOptions() {
  $.get(origin + "/api/barang", function (data) {
    if (!Array.isArray(data)) return;

    const options = data
      .map((item) => `<option value="${item.kode}">${item.nama}</option>`)
      .join("");

    // Untuk form tambah
    $("#add-barang_kode").html(
      '<option value="" disabled selected>Pilih barang</option>' + options
    );
    // Untuk form edit
    $("#edit-barang_kode").html(
      '<option value="" disabled selected>Pilih barang</option>' + options
    );

    // Re-init select setelah isi
    M.FormSelect.init(document.querySelectorAll("select"));
  });
}

$(document).ready(function () {
  loadBarangOptions();
  cloud
    .add(origin + "/api/peminjaman", {
      name: "peminjaman",
      callback: (data) => {
        table.peminjaman.ajax.reload();
      },
    })
    .then((peminjaman) => {});
  $(".preloader").slideUp();
});
