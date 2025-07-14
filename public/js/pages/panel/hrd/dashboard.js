const elbarang = $("[data-entity=barang]");
const elkategori = $("[data-entity=kategori]");
$(document).ready(function () {
  cloud
    .add(origin + "/api/barang", {
      name: "barang",
      callback: (data) => {
        elbarang.text(service.length).counterUp();
      },
    })
    .then((service) => {
      elbarang.text(service.length).counterUp();
    });
  cloud
    .add(origin + "/api/kategori", {
      name: "kategori",
      callback: (data) => {
        elkategori.text(service.length).counterUp();
      },
    })
    .then((service) => {
      elkategori.text(service.length).counterUp();
    });
});
