const table = {
  kriteria_klasterisasi: $("#table-kriteria-klasterisasi").DataTable({
    responsive: true,
    ajax: {
      url: baseUrl + "/api/kriteria-klasterisasi",
      dataSrc: "",
    },
    order: [
      [0, "asc"],
      [3, "asc"],
    ],
    columns: [
      {
        title: "#",
        data: "id",
        render: function (data, type, row, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        },
      },
      { title: "Nama", data: "nama" },
      { title: "Kode", data: "kode" },

      {
        title: "Aksi",
        data: "id",
        render: (data, type, row) => {
          return `<div class="table-control">
          <a role="button" class="btn waves-effect waves-light btn-action btn-popup orange darken-2" data-target="edit" data-action="edit" data-id="${data}"><i class="material-icons">edit</i></a>
          <a role="button" class="btn waves-effect waves-light btn-action red" data-action="delete" data-id="${data}"><i class="material-icons">delete</i></a>
          </div>`;
        },
      },
    ],
  }),
};

$("form#form-add").on("submit", function (e) {
  e.preventDefault();

  const form = this;
  const formData = new FormData(form); // Ini akan otomatis menangkap file juga

  const elements = form.elements;
  for (let i = 0, len = elements.length; i < len; ++i) {
    elements[i].readOnly = true;
  }

  $.ajax({
    type: "POST",
    url: baseUrl + "/api/kriteria-klasterisasi",
    data: formData,
    contentType: false, // WAJIB agar FormData bekerja
    processData: false, // WAJIB agar FormData tidak diubah jadi query string
    success: (data) => {
      form.reset();
      cloud.pull("kriteria-klasterisasi");
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
            url: baseUrl + "/api/kriteria-klasterisasi/" + id,
            cache: false,
            success: (data) => {
              table.kriteria_klasterisasi.ajax.reload();
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
      let dataEdit = cloud.get("kriteria-klasterisasi").find((x) => x.id == id);
      console.log(dataEdit);
      $("form#form-edit")[0].reset();
      $("form#form-edit").find("input[name=id]").val(dataEdit.id);
      $.each(dataEdit, function (field, val) {
        $("form#form-edit").find(`[name=${field}]`).val(val);
      });
      M.updateTextFields();
      M.textareaAutoResize($("textarea"));
      M.FormSelect.init(document.querySelectorAll("select"));
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
    url: baseUrl + "/api/kriteria-klasterisasi/" + data.id,
    data: data,
    cache: false,
    success: (data) => {
      $(this)[0].reset();
      cloud.pull("kriteria-klasterisasi");
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

$(document).ready(function () {
  cloud
    .add(baseUrl + "/api/kriteria-klasterisasi", {
      name: "kriteria-klasterisasi",
      callback: (data) => {
        table.kriteria_klasterisasi.ajax.reload();
      },
    })
    .then((kriteria_klasterisasi) => {});
  $(".preloader").slideUp();
});
