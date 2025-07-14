const table = {
  wisata: $("#table-wisata").DataTable({
    responsive: true,
    ajax: {
      url: origin + "/api/wisata",
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
      { title: "Alamat", data: "alamat" },
      { title: "Deskripsi", data: "deskripsi" },
      { title: "Latitude", data: "latitude" },
      { title: "Longitude", data: "longitude" },
      { title: "Klaster", data: "klaster" },
      {
        title: "Gambar",
        data: "gambar",
        render: (data, type, row) => {
          return `<img src="${origin}/api/v2/source/storage/${data}" class="responsive-img" style="max-height: 100px; max-width: 100px;" />`;
        },
      },
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

let modalMap;
let modalMarker;

function initModalMap() {
  // Hapus map jika sudah ada
  if (modalMap) {
    modalMap.remove();
  }

  // Inisialisasi map
  modalMap = L.map("modalMapContainer").setView([-6.2, 106.816666], 10);

  // OpenStreetMap
  const osm = L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
      attribution: "&copy; OpenStreetMap contributors",
    }
  ).addTo(modalMap);

  // Google Maps (via gtile proxies)
  const googleSat = L.tileLayer(
    "http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
    {
      maxZoom: 20,
      subdomains: ["mt0", "mt1", "mt2", "mt3"],
      attribution: "Google Satellite",
    }
  );

  // Esri
  const esri = L.tileLayer(
    "https://server.arcgisonline.com/ArcGIS/rest/services/" +
      "World_Street_Map/MapServer/tile/{z}/{y}/{x}",
    {
      attribution: "Tiles &copy; Esri",
    }
  );

  // Topo
  const topo = L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", {
    attribution: "OpenTopoMap",
  });

  // Layer switcher
  const baseLayers = {
    OpenStreetMap: osm,
    "Google Satellite": googleSat,
    "Esri Street": esri,
    "Topo Map": topo,
  };

  L.control.layers(baseLayers).addTo(modalMap);

  // Geocoder (search lokasi)
  const geocoder = L.Control.geocoder({
    defaultMarkGeocode: false,
    position: "topleft",
    placeholder: "Cari lokasi...",
    errorMessage: "Lokasi tidak ditemukan",
  })
    .on("markgeocode", function (e) {
      const center = e.geocode.center;
      modalMap.setView(center, 15);

      if (modalMarker) {
        modalMap.removeLayer(modalMarker);
      }

      modalMarker = L.marker(center).addTo(modalMap);
      $("#add-latitude").val(center.lat);
      $("#add-longitude").val(center.lng);
    })
    .addTo(modalMap);

  // Klik peta untuk pilih lokasi
  modalMap.on("click", function (e) {
    const { lat, lng } = e.latlng;

    if (modalMarker) {
      modalMap.removeLayer(modalMarker);
    }

    modalMarker = L.marker([lat, lng]).addTo(modalMap);

    // Isi form latitude dan longitude
    $("#add-latitude").val(lat);
    $("#add-longitude").val(lng);
  });
}

// Saat modal dibuka, jalankan peta
$(".modal-trigger[data-target='modal-map']").on("click", function () {
  setTimeout(() => {
    initModalMap();
  }, 500); // delay agar container modal sudah render
});

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
    url: origin + "/api/wisata",
    data: formData,
    contentType: false, // WAJIB agar FormData bekerja
    processData: false, // WAJIB agar FormData tidak diubah jadi query string
    success: (data) => {
      form.reset();
      cloud.pull("wisata");
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
            url: origin + "/api/wisata/" + id,
            cache: false,
            success: (data) => {
              table.wisata.ajax.reload();
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
      let dataEdit = cloud.get("wisata").find((x) => x.id == id);
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
    url: origin + "/api/wisata/" + data.id,
    data: data,
    cache: false,
    success: (data) => {
      $(this)[0].reset();
      cloud.pull("wisata");
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
    .add(origin + "/api/wisata", {
      name: "wisata",
      callback: (data) => {
        table.wisata.ajax.reload();
      },
    })
    .then((wisata) => {});
  $(".preloader").slideUp();
});
