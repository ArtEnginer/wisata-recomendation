$(document).ready(function () {
  document.addEventListener("DOMContentLoaded", function () {
    var elems = document.querySelectorAll(".tabs");
    M.Tabs.init(elems);
  });

  let map;
  let markers = [];
  let userMarker = null;
  let wisataData = [];
  let klasterisasiCriteria = [];
  let perengkinganCriteria = [];
  let userLocation = null;
  let distanceMatrix = [];
  let clusterResults = [];
  let routeLines = [];
  let geocoder;
  let recommendations = []; // Add this at the top with other variable declarations
  let selectedCentroids = []; // Add this to store manually selected centroids

  // Initialize map with OpenStreetMap and Nominatim for geocoding
  function initMap() {
    map = L.map("map").setView([-7.5838902875197, 110.81321640838382], 14);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    // Initialize Nominatim geocoder
    geocoder = L.Control.geocoder({
      defaultMarkGeocode: false,
      position: "topleft",
      placeholder: "Cari lokasi...",
      errorMessage: "Lokasi tidak ditemukan",
    })
      .on("markgeocode", function (e) {
        const { center, name } = e.geocode;
        setUserLocation(center.lat, center.lng);
        $("#user-lat").val(center.lat);
        $("#user-lng").val(center.lng);
        $("#location-name").val(name);
        map.fitBounds(e.geocode.bbox);
      })
      .addTo(map);

    // Add click event for map
    map.on("click", function (e) {
      setUserLocation(e.latlng.lat, e.latlng.lng);
      $("#user-lat").val(e.latlng.lat);
      $("#user-lng").val(e.latlng.lng);

      // Reverse geocode to get location name
      fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`
      )
        .then((response) => response.json())
        .then((data) => {
          const name = data.display_name || "Lokasi terpilih";
          $("#location-name").val(name);
        });
    });

    // Plot wisata setelah peta siap
    setTimeout(() => {
      if (wisataData.length > 0) {
        plotWisataOnMap(wisataData);
      }
    }, 1000);
  }

  initMap();

  // Set user location on map
  function setUserLocation(lat, lng) {
    userLocation = { latitude: lat, longitude: lng };

    // Clear previous user marker
    if (userMarker) {
      map.removeLayer(userMarker);
    }

    // Clear previous routes
    clearRouteLines();

    // Add new marker
    userMarker = L.marker([lat, lng], {
      icon: L.divIcon({
        className: "user-icon",
        html: '<div style="background-color: purple; width: 20px; height: 20px; border-radius: 50%;"></div>',
        iconSize: [20, 20],
      }),
      zIndexOffset: 1000,
    })
      .addTo(map)
      .bindPopup("Lokasi Anda")
      .openPopup();

    // Pan to user location
    map.panTo([lat, lng]);
  }

  // Clear all route lines from map
  function clearRouteLines() {
    routeLines.forEach((line) => map.removeLayer(line));
    routeLines = [];
  }

  // Load data
  cloud
    .add(baseUrl + "/api/wisata", {
      name: "wisata",
    })
    .then((wisata) => {
      wisataData = wisata;
      console.log("Wisata data loaded", wisata);

      // Plot wisata immediately after load
      plotWisataOnMap(wisata);

      // Pre-calculate distance matrix
      distanceMatrix = floydWarshall(wisataData);

      // Juga plot setelah peta siap (double safeguard)
      setTimeout(() => plotWisataOnMap(wisata), 500);
    });

  cloud
    .add(baseUrl + "/api/kriteria-perengkingan", {
      name: "kriteria-perengkingan",
    })
    .then((kriteria) => {
      perengkinganCriteria = kriteria;
      console.log("Kriteria Perengkingan data loaded", kriteria);
    });

  cloud
    .add(baseUrl + "/api/kriteria-klasterisasi", {
      name: "kriteria-klasterisasi",
    })
    .then((kriteria) => {
      klasterisasiCriteria = kriteria;
      console.log("Kriteria Klasterisasi data loaded", kriteria);
    });

  // Plot wisata on map
  function plotWisataOnMap(wisata) {
    if (!map || !wisata || wisata.length === 0) return;

    // Clear existing markers hanya jika ada
    if (markers.length > 0) {
      markers.forEach((marker) => map.removeLayer(marker));
    }
    markers = [];

    // Tambahkan pengecekan koordinat valid
    wisata.forEach((item) => {
      const lat = parseFloat(item.latitude);
      const lng = parseFloat(item.longitude);

      if (isNaN(lat) || isNaN(lng)) {
        console.error("Invalid coordinates for", item.nama);
        return;
      }

      const marker = L.marker([lat, lng], {
        icon: getDefaultIcon(), // Gunakan icon default sebelum clustering
        riseOnHover: true,
      })
        .addTo(map)
        .bindPopup(`<b>${item.nama}</b><br>${item.deskripsi}`);

      markers.push(marker);
    });
  }

  function getDefaultIcon() {
    return L.divIcon({
      className: "default-icon",
      html: '<div style="background-color: green; width: 20px; height: 20px; border-radius: 50%;"></div>',
      iconSize: [20, 20],
    });
  }

  // K-Means Clustering implementation with manual centroid selection
  function kMeansClustering(
    data,
    k = 3,
    maxIterations = 100,
    manualCentroids = null
  ) {
    // Extract features for clustering
    const features = data.map((item) => {
      return item.nilai_kriteria_klasterisasi.map((nilai) =>
        parseFloat(nilai.nilai)
      );
    });

    // Step 1: Initialize centroids - use manual selection if provided
    let centroids = [];
    if (manualCentroids && manualCentroids.length === k) {
      // Use manually selected centroids
      centroids = manualCentroids.map((index) => [...features[index]]);
    } else {
      // Fallback to random selection
      const randomIndices = [];
      while (randomIndices.length < k) {
        const randomIndex = Math.floor(Math.random() * data.length);
        if (!randomIndices.includes(randomIndex)) {
          randomIndices.push(randomIndex);
          centroids.push([...features[randomIndex]]);
        }
      }
    }

    let clusters = Array(data.length).fill(-1);
    let prevClusters = Array(data.length).fill(-2);
    let iterations = 0;
    let steps = [];

    const centroidSelection = manualCentroids
      ? manualCentroids.map((index) => data[index].nama).join(", ")
      : "Acak";

    steps.push({
      title: "Inisialisasi Awal",
      description: `Memilih ${k} centroid (${centroidSelection}):`,
      centroids: [...centroids],
      clusters: [...clusters],
      selectedWisata: manualCentroids
        ? manualCentroids.map((index) => data[index].nama)
        : null,
    });

    while (!arraysEqual(clusters, prevClusters) && iterations < maxIterations) {
      prevClusters = [...clusters];

      // Step 2: Assign each point to the nearest centroid
      const assignments = [];
      for (let i = 0; i < features.length; i++) {
        let minDist = Infinity;
        let bestCluster = -1;
        let distances = [];

        for (let j = 0; j < centroids.length; j++) {
          const dist = euclideanDistance(features[i], centroids[j]);
          distances.push(dist);
          if (dist < minDist) {
            minDist = dist;
            bestCluster = j;
          }
        }

        clusters[i] = bestCluster;
        assignments.push({
          point: features[i],
          distances: distances,
          cluster: bestCluster,
        });
      }

      steps.push({
        title: `Iterasi ${iterations + 1} - Penugasan Klaster`,
        description:
          "Menghitung jarak setiap titik ke centroid dan menetapkan ke klaster terdekat:",
        centroids: [...centroids],
        clusters: [...clusters],
        assignments: [...assignments],
      });

      // Step 3: Update centroids
      const clusterSums = Array(k)
        .fill()
        .map(() => Array(features[0].length).fill(0));
      const clusterCounts = Array(k).fill(0);

      for (let i = 0; i < features.length; i++) {
        const cluster = clusters[i];
        clusterCounts[cluster]++;

        for (let j = 0; j < features[i].length; j++) {
          clusterSums[cluster][j] += features[i][j];
        }
      }

      const newCentroids = [];
      for (let i = 0; i < k; i++) {
        newCentroids.push([]);
        if (clusterCounts[i] > 0) {
          for (let j = 0; j < features[0].length; j++) {
            newCentroids[i][j] = clusterSums[i][j] / clusterCounts[i];
          }
        } else {
          // If a cluster has no points, keep the previous centroid
          newCentroids[i] = [...centroids[i]];
        }
      }

      steps.push({
        title: `Iterasi ${iterations + 1} - Pembaruan Centroid`,
        description:
          "Menghitung centroid baru sebagai rata-rata titik dalam setiap klaster:",
        oldCentroids: [...centroids],
        newCentroids: [...newCentroids],
        clusterCounts: [...clusterCounts],
      });

      centroids = newCentroids;
      iterations++;
    }

    // Calculate silhouette score for validation
    const silhouetteScore = calculateSilhouetteScore(
      features,
      clusters,
      centroids
    );

    return {
      clusters: clusters,
      centroids: centroids,
      silhouetteScore: silhouetteScore,
      steps: steps,
      iterations: iterations,
    };
  }

  // Add these helper functions at the beginning of your dashboard.js file

  // Calculate distance between two geographic points using Haversine formula
  function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth radius in km
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLon = ((lon2 - lon1) * Math.PI) / 180;
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c; // Distance in km
  }

  // Check if two arrays are equal
  function arraysEqual(a, b) {
    if (a === b) return true;
    if (a == null || b == null) return false;
    if (a.length !== b.length) return false;

    for (let i = 0; i < a.length; ++i) {
      if (a[i] !== b[i]) return false;
    }
    return true;
  }

  // Calculate Euclidean distance between two points
  function euclideanDistance(a, b) {
    let sum = 0;
    for (let i = 0; i < a.length; i++) {
      sum += Math.pow(a[i] - b[i], 2);
    }
    return Math.sqrt(sum);
  }

  // Calculate silhouette score for clustering validation
  function calculateSilhouetteScore(features, clusters, centroids) {
    let totalScore = 0;

    for (let i = 0; i < features.length; i++) {
      const currentCluster = clusters[i];

      // Calculate a(i) - average distance to other points in same cluster
      let a = 0;
      let sameClusterCount = 0;

      for (let j = 0; j < features.length; j++) {
        if (i !== j && clusters[j] === currentCluster) {
          a += euclideanDistance(features[i], features[j]);
          sameClusterCount++;
        }
      }

      a = sameClusterCount > 0 ? a / sameClusterCount : 0;

      // Calculate b(i) - smallest average distance to other clusters
      let b = Infinity;

      for (let k = 0; k < centroids.length; k++) {
        if (k !== currentCluster) {
          let distSum = 0;
          let count = 0;

          for (let j = 0; j < features.length; j++) {
            if (clusters[j] === k) {
              distSum += euclideanDistance(features[i], features[j]);
              count++;
            }
          }

          const avgDist = count > 0 ? distSum / count : Infinity;
          if (avgDist < b) {
            b = avgDist;
          }
        }
      }

      // Calculate silhouette for this point
      const s = (b - a) / Math.max(a, b);
      totalScore += isNaN(s) ? 0 : s;
    }

    return totalScore / features.length;
  }

  // Interpret silhouette score values
  function interpretSilhouetteScore(score) {
    if (score > 0.7) return "Struktur klaster kuat";
    if (score > 0.5) return "Struktur klaster masuk akal";
    if (score > 0.25) return "Struktur klaster lemah";
    return "Tidak ada struktur klaster yang substansial";
  }

  // Get color for cluster visualization
  function getClusterColor(klaster) {
    const colors = ["red", "blue", "green"]; // 0: tinggi, 1: sedang, 2: rendah
    return colors[parseInt(klaster)] || "gray";
  }

  // Get name for cluster
  function getClusterName(klaster) {
    const names = ["Tinggi", "Sedang", "Rendah"];
    return names[parseInt(klaster)] || "Unknown";
  }
  // Floyd-Warshall algorithm for shortest paths with route visualization
  function floydWarshall(locations) {
    const n = locations.length;
    const dist = Array(n)
      .fill()
      .map(() => Array(n).fill(Infinity));
    const next = Array(n)
      .fill()
      .map(() => Array(n).fill(null));
    const steps = []; // To store calculation steps

    // Initialize distances and next pointers
    steps.push({
      title: "Inisialisasi",
      description:
        "Mengisi matriks jarak awal dengan nilai tak terhingga dan matriks next dengan null",
      dist: JSON.parse(JSON.stringify(dist)),
      next: JSON.parse(JSON.stringify(next)),
    });

    for (let i = 0; i < n; i++) {
      dist[i][i] = 0;
      for (let j = i + 1; j < n; j++) {
        const d = haversineDistance(
          locations[i].latitude,
          locations[i].longitude,
          locations[j].latitude,
          locations[j].longitude
        );
        dist[i][j] = d;
        dist[j][i] = d;
        next[i][j] = j;
        next[j][i] = i;
      }
    }

    steps.push({
      title: "Set Jarak Awal",
      description:
        "Menghitung jarak langsung antar lokasi menggunakan formula Haversine",
      dist: JSON.parse(JSON.stringify(dist)),
      next: JSON.parse(JSON.stringify(next)),
    });

    // Floyd-Warshall algorithm
    for (let k = 0; k < n; k++) {
      const stepChanges = [];

      for (let i = 0; i < n; i++) {
        for (let j = 0; j < n; j++) {
          if (dist[i][j] > dist[i][k] + dist[k][j]) {
            const oldDist = dist[i][j];
            dist[i][j] = dist[i][k] + dist[k][j];
            next[i][j] = next[i][k];

            stepChanges.push({
              i: i,
              j: j,
              k: k,
              oldDist: oldDist,
              newDist: dist[i][j],
              path: getPath(i, j, next),
            });
          }
        }
      }

      if (stepChanges.length > 0) {
        steps.push({
          title: `Iterasi k = ${k} (${locations[k].nama || "Lokasi " + k})`,
          description: "Memperbarui jarak terpendek melalui node perantara",
          changes: [...stepChanges],
          dist: JSON.parse(JSON.stringify(dist)),
          next: JSON.parse(JSON.stringify(next)),
        });
      }
    }

    steps.push({
      title: "Hasil Final",
      description: "Matriks jarak terpendek setelah semua iterasi",
      dist: JSON.parse(JSON.stringify(dist)),
      next: JSON.parse(JSON.stringify(next)),
    });

    return {
      distances: dist,
      next: next,
      steps: steps,
    };
  }

  function showFloydWarshallDetails(result, locations) {
    let html = "<h4>Detail Perhitungan Floyd-Warshall</h4>";

    result.steps.forEach((step, index) => {
      html += `<div class="card-panel grey lighten-4"><h5>${step.title}</h5>`;
      html += `<p>${step.description}</p>`;

      if (step.changes) {
        html += "<h6>Perubahan Jarak:</h6>";
        html +=
          '<table class="striped"><thead><tr><th>Dari</th><th>Ke</th><th>Melalui</th><th>Jarak Lama</th><th>Jarak Baru</th><th>Path</th></tr></thead><tbody>';

        step.changes.forEach((change) => {
          html += `<tr>
                    <td>${locations[change.i].nama || "Lokasi " + change.i}</td>
                    <td>${locations[change.j].nama || "Lokasi " + change.j}</td>
                    <td>${locations[change.k].nama || "Lokasi " + change.k}</td>
                    <td>${change.oldDist.toFixed(2)} km</td>
                    <td>${change.newDist.toFixed(2)} km</td>
                    <td>${change.path
                      .map((p) => locations[p].nama || p)
                      .join(" → ")}</td>
                </tr>`;
        });

        html += "</tbody></table>";
      }

      if (index === 1 || index === result.steps.length - 1) {
        html += "<h6>Matriks Jarak:</h6>";
        html +=
          '<div style="overflow: auto;"><table class="striped"><thead><tr><th></th>';

        // Header row
        locations.forEach((loc, i) => {
          html += `<th>${loc.nama || i}</th>`;
        });
        html += "</tr></thead><tbody>";

        // Data rows
        step.dist.forEach((row, i) => {
          html += `<tr><td><strong>${locations[i].nama || i}</strong></td>`;
          row.forEach((val) => {
            html += `<td>${val === Infinity ? "∞" : val.toFixed(2)}</td>`;
          });
          html += "</tr>";
        });

        html += "</tbody></table></div>";
      }

      html += "</div>";
    });

    return html;
  }

  // Get path from node u to node v
  function getPath(u, v, next) {
    if (next[u][v] === null) return [];
    const path = [u];
    while (u !== v) {
      u = next[u][v];
      path.push(u);
    }
    return path;
  }

  // Visualize route on map
  function visualizeRoute(
    startIndex,
    endIndex,
    locations,
    next,
    color = "blue"
  ) {
    const path = getPath(startIndex, endIndex, next);
    const coordinates = [];

    for (let i = 0; i < path.length; i++) {
      const point = locations[path[i]];
      coordinates.push([
        parseFloat(point.latitude),
        parseFloat(point.longitude),
      ]);
    }

    const line = L.polyline(coordinates, {
      color: color,
      weight: 3,
      opacity: 0.7,
      dashArray: "5, 5",
    }).addTo(map);

    routeLines.push(line);

    // Add direction markers
    for (let i = 0; i < coordinates.length - 1; i++) {
      const midLat = (coordinates[i][0] + coordinates[i + 1][0]) / 2;
      const midLng = (coordinates[i][1] + coordinates[i + 1][1]) / 2;

      L.marker([midLat, midLng], {
        icon: L.divIcon({
          className: "direction-icon",
          html: '<div style="transform: rotate(45deg);">→</div>',
          iconSize: [20, 20],
        }),
      }).addTo(map);
    }

    return line;
  }

  // Function to show centroid selection modal
  function showCentroidSelectionModal() {
    if (wisataData.length === 0) {
      alert("Data wisata belum dimuat!");
      return;
    }

    let modalHtml = `
      <div id="centroid-modal" class="modal" style="max-height: 80%;">
        <div class="modal-content">
          <h4>Pilih Centroid Awal untuk Klasterisasi</h4>
          <p>Pilih 3 wisata sebagai centroid awal untuk klasterisasi K-Means:</p>
          
          <div class="row">
            <div class="col s12">
              <table class="striped">
                <thead>
                  <tr>
                    <th>Pilih</th>
                    <th>Nama Wisata</th>
                    ${klasterisasiCriteria
                      .map((crit) => `<th>${crit.nama}</th>`)
                      .join("")}
                  </tr>
                </thead>
                <tbody>
                  ${wisataData
                    .map(
                      (item, index) => `
                    <tr>
                      <td>
                        <label>
                          <input type="checkbox" class="centroid-checkbox" value="${index}" />
                          <span></span>
                        </label>
                      </td>
                      <td>${item.nama}</td>
                      ${item.nilai_kriteria_klasterisasi
                        .map((nilai) => `<td>${nilai.nilai}</td>`)
                        .join("")}
                    </tr>
                  `
                    )
                    .join("")}
                </tbody>
              </table>
            </div>
          </div>

          <div class="row">
            <div class="col s12">
              <p><strong>Centroid yang dipilih:</strong></p>
              <div id="selected-centroids" class="chip-container"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#!" class="modal-close waves-effect waves-red btn-flat">Batal</a>
          <a href="#!" id="btn-start-clustering" class="waves-effect waves-green btn">Mulai Klasterisasi</a>
        </div>
      </div>
    `;

    // Remove existing modal if any
    $("#centroid-modal").remove();

    // Add modal to body
    $("body").append(modalHtml);

    // Initialize modal
    const modal = M.Modal.init(document.getElementById("centroid-modal"), {
      dismissible: false,
    });

    // Show modal
    modal.open();

    // Handle checkbox selection
    $(".centroid-checkbox").on("change", function () {
      const selectedCount = $(".centroid-checkbox:checked").length;

      if (selectedCount > 3) {
        $(this).prop("checked", false);
        M.toast({ html: "Maksimal 3 centroid dapat dipilih!" });
        return;
      }

      updateSelectedCentroidsDisplay();

      // Enable/disable start button
      $("#btn-start-clustering").toggleClass("disabled", selectedCount !== 3);
    });

    // Handle start clustering button
    $("#btn-start-clustering").on("click", function () {
      if ($(this).hasClass("disabled")) {
        M.toast({ html: "Pilih tepat 3 centroid!" });
        return;
      }

      selectedCentroids = $(".centroid-checkbox:checked")
        .map(function () {
          return parseInt($(this).val());
        })
        .get();

      modal.close();
      performClustering();
    });
  }

  // Function to update selected centroids display
  function updateSelectedCentroidsDisplay() {
    const selectedIndices = $(".centroid-checkbox:checked")
      .map(function () {
        return parseInt($(this).val());
      })
      .get();

    const container = $("#selected-centroids");
    container.empty();

    selectedIndices.forEach((index, i) => {
      const chip = $(`
        <div class="chip">
          Centroid ${i + 1}: ${wisataData[index].nama}
          <i class="close material-icons" data-index="${index}">close</i>
        </div>
      `);
      container.append(chip);
    });

    // Handle chip removal
    $(".chip .close").on("click", function () {
      const index = $(this).data("index");
      $(`.centroid-checkbox[value="${index}"]`).prop("checked", false);
      updateSelectedCentroidsDisplay();
      $("#btn-start-clustering").addClass("disabled");
    });
  }

  // Function to perform clustering with selected centroids
  function performClustering() {
    if (wisataData.length === 0 || klasterisasiCriteria.length === 0) {
      alert("Data wisata atau kriteria klasterisasi belum dimuat!");
      return;
    }

    const result = kMeansClustering(wisataData, 3, 100, selectedCentroids);
    clusterResults = {
      clusters: result.clusters,
      centroids: result.centroids,
      silhouetteScore: result.silhouetteScore,
      steps: result.steps,
      iterations: result.iterations,
      recommendations: recommendations,
      selectedCentroids: selectedCentroids, // Store selected centroids info
    };

    // Update wisata data with new clusters
    wisataData.forEach((item, index) => {
      item.klaster = result.clusters[index];
    });

    // Plot with new clusters
    plotWisataOnMap(wisataData);

    // Show clustering results
    showClusterResults(result);
    if (result.silhouetteScore !== undefined) {
      $("#accuracy-tab").html(
        '<canvas id="accuracyChart" height="300"></canvas>'
      );
    }

    console.log("Clustering completed with manual centroids", result);
  }

  // Event handlers
  $("#btn-cluster").click(function () {
    showCentroidSelectionModal();
  });

  // Show cluster results with detailed steps
  function showClusterResults(result) {
    const clusterNames = ["Tinggi", "Sedang", "Rendah"];
    const clusterCounts = Array(result.centroids.length).fill(0);
    const clusterMembers = Array(result.centroids.length)
      .fill()
      .map(() => []);

    // Count cluster members and collect wisata names for each cluster
    result.clusters.forEach((c, index) => {
      clusterCounts[c]++;
      clusterMembers[c].push(wisataData[index].nama);
    });

    let html = "<h4>Hasil Klasterisasi</h4>";

    // Show selected centroids info
    if (selectedCentroids.length > 0) {
      html += "<div class='card-panel blue lighten-5'>";
      html += "<h6>Centroid Awal yang Dipilih:</h6>";
      selectedCentroids.forEach((index, i) => {
        html += `<p>Centroid ${i + 1}: ${wisataData[index].nama}</p>`;
      });
      html += "</div>";
    }

    html += `<p>Jumlah Iterasi: ${result.iterations}</p>`;
    html += `<p>Silhouette Score: ${result.silhouetteScore.toFixed(
      4
    )} (${interpretSilhouetteScore(result.silhouetteScore)})</p>`;
    html +=
      '<table class="table table-bordered"><thead><tr><th>Klaster</th><th>Jumlah Wisata</th><th>Nama Wisata</th><th>Centroid</th></tr></thead><tbody>';

    result.centroids.forEach((centroid, idx) => {
      html += `<tr><td>${clusterNames[idx]}</td><td>${clusterCounts[idx]}</td>`;

      // Add wisata names for this cluster
      html += "<td>";
      if (clusterMembers[idx].length > 0) {
        html += clusterMembers[idx].join(", ");
      } else {
        html += "-";
      }
      html += "</td>";

      // Add centroid values
      html += "<td>";
      centroid.forEach((val, i) => {
        html += `${klasterisasiCriteria[i].nama}: ${val.toFixed(2)}<br>`;
      });
      html += "</td></tr>";
    });

    html += "</tbody></table>";

    $("#cluster-results").html(html);

    // Show cluster details
    html =
      '<h4>Detail Klaster</h4><table class="table table-bordered"><thead><tr><th>No</th><th>Wisata</th><th>Klaster</th>';
    klasterisasiCriteria.forEach((crit) => {
      html += `<th>${crit.nama}</th>`;
    });
    html += "</tr></thead><tbody>";

    wisataData.forEach((item, idx) => {
      html += `<tr><td>${idx + 1}</td><td>${item.nama}</td><td>${
        clusterNames[result.clusters[idx]]
      }</td>`;
      item.nilai_kriteria_klasterisasi.forEach((nilai) => {
        html += `<td>${nilai.nilai}</td>`;
      });
      html += "</tr>";
    });

    html += "</tbody></table>";
    $("#cluster-details").html(html);

    // Show detailed calculation steps
    showCalculationSteps(result);
    if (result.silhouetteScore !== undefined) {
      // Jika sedang di tab akurasi, update chart
      if ($("#accuracy-tab:visible").length) {
        showAccuracyChart();
      }
    }
  }

  // Interpret silhouette score
  function interpretSilhouetteScore(score) {
    if (score > 0.7) return "Struktur klaster kuat";
    if (score > 0.5) return "Struktur klaster masuk akal";
    if (score > 0.25) return "Struktur klaster lemah";
    return "Tidak ada struktur klaster yang substansial";
  }

  // Show detailed calculation steps for K-Means
  function showCalculationSteps(result) {
    let html = "<h4>Perhitungan Detail K-Means</h4>";

    result.steps.forEach((step, stepIdx) => {
      html += `<div class="mb-4"><h5>${step.title}</h5>`;
      html += `<p>${step.description}</p>`;

      // Show selected wisata for initial centroids
      if (step.selectedWisata) {
        html +=
          "<p><strong>Wisata yang dipilih sebagai centroid awal:</strong></p>";
        html += "<ul>";
        step.selectedWisata.forEach((nama, idx) => {
          html += `<li>Centroid ${idx + 1}: ${nama}</li>`;
        });
        html += "</ul>";
      }

      if (step.assignments) {
        html += '<table class="table table-bordered"><thead><tr><th>Titik</th>';
        for (let i = 0; i < result.centroids.length; i++) {
          html += `<th>Jarak ke Centroid ${i + 1}</th>`;
        }
        html += "<th>Klaster Terdekat</th></tr></thead><tbody>";

        step.assignments.forEach((assignment, idx) => {
          html += `<tr><td>${wisataData[idx].nama}</td>`;
          assignment.distances.forEach((dist) => {
            html += `<td>${dist.toFixed(2)}</td>`;
          });
          html += `<td>${assignment.cluster + 1}</td></tr>`;
        });

        html += "</tbody></table>";
      }

      if (step.centroids) {
        html +=
          '<h6>Centroid:</h6><table class="table table-bordered"><thead><tr><th>Centroid</th>';
        klasterisasiCriteria.forEach((crit) => {
          html += `<th>${crit.nama}</th>`;
        });
        html += "</tr></thead><tbody>";

        step.centroids.forEach((centroid, idx) => {
          html += `<tr><td>${idx + 1}</td>`;
          centroid.forEach((val) => {
            html += `<td>${val.toFixed(2)}</td>`;
          });
          html += "</tr>";
        });

        html += "</tbody></table>";
      }

      if (step.newCentroids && step.oldCentroids) {
        html +=
          '<h6>Perubahan Centroid:</h6><table class="table table-bordered"><thead><tr><th>Centroid</th>';
        klasterisasiCriteria.forEach((crit) => {
          html += `<th>${crit.nama}</th>`;
        });
        html += "<th>Jumlah Anggota</th></tr></thead><tbody>";

        step.newCentroids.forEach((centroid, idx) => {
          html += `<tr><td>${idx + 1}</td>`;
          centroid.forEach((val) => {
            html += `<td>${val.toFixed(2)}</td>`;
          });
          html += `<td>${
            step.clusterCounts ? step.clusterCounts[idx] : "-"
          }</td></tr>`;
        });

        html += "</tbody></table>";
      }

      html += "</div>";
    });

    $("#calculation-steps").html(html);
  }

  // Calculate shortest paths and visualize
  $("#btn-shortest-path").click(function () {
    if (!userLocation) {
      alert("Atur lokasi Anda terlebih dahulu!");
      return;
    }

    if (wisataData.length === 0) {
      alert("Data wisata belum dimuat!");
      return;
    }

    // Add user location temporarily to wisata data for distance calculation
    const tempLocations = [
      ...wisataData,
      {
        latitude: userLocation.latitude,
        longitude: userLocation.longitude,
        nama: "Lokasi Anda",
      },
    ];

    console.log(tempLocations);

    // Recalculate distance matrix with user location
    const result = floydWarshall(tempLocations);
    distanceMatrix = {
      distances: result.distances,
      next: result.next,
    };

    const userLocationIndex = wisataData.length; // Last index is user location
    const clusterNames = ["Tinggi", "Sedang", "Rendah"];

    // Clear previous routes
    clearRouteLines();

    // Show distance results with route visualization buttons
    let html = "<h4>Jarak ke Wisata</h4>";

    // Add button to show calculation details
    html +=
      '<a class="waves-effect waves-light btn blue" id="btn-show-fw-calculation">Tampilkan Perhitungan Floyd-Warshall</a>';
    html +=
      '<div id="fw-calculation-details" class="section" style="display:none;"></div>';

    html +=
      '<table class="table table-bordered"><thead><tr><th>No</th><th>Wisata</th><th>Klaster</th><th>Jarak (km)</th><th>Aksi</th></tr></thead><tbody>';

    wisataData.forEach((item, index) => {
      const distance = result.distances[userLocationIndex][index];
      html += `<tr>
            <td>${index + 1}</td>
            <td>${item.nama}</td>
            <td>${clusterNames[parseInt(item.klaster)]}</td>
            <td>${distance.toFixed(2)}</td>
            <td><button class="btn btn-sm btn-info btn-show-route" data-index="${index}">Tampilkan Rute</button></td>
        </tr>`;
    });

    html += "</tbody></table>";
    $("#distance-results").html(html);

    // Store the calculation details for later display
    $("#distance-results").data("fw-details", {
      result: result,
      locations: tempLocations,
    });
  });

  // Add handler for showing Floyd-Warshall calculation details
  $(document).on("click", "#btn-show-fw-calculation", function () {
    const details = $("#distance-results").data("fw-details");
    if (!details) return;

    const html = showFloydWarshallDetails(details.result, details.locations);
    $("#fw-calculation-details").html(html).slideDown();

    // Scroll to the details
    $("html, body").animate(
      {
        scrollTop: $("#fw-calculation-details").offset().top - 20,
      },
      500
    );
  });
  // Handle route visualization button clicks
  $(document).on("click", ".btn-show-route", function () {
    const wisataIndex = $(this).data("index");
    const userLocationIndex = wisataData.length;

    // Clear previous routes
    clearRouteLines();

    // Visualize route
    visualizeRoute(
      userLocationIndex,
      wisataIndex,
      [
        ...wisataData,
        {
          latitude: userLocation.latitude,
          longitude: userLocation.longitude,
          nama: "Lokasi Anda",
        },
      ],
      distanceMatrix.next,
      "#ff7800"
    );

    // Pan to show the route
    const start = [userLocation.latitude, userLocation.longitude];
    const end = [
      wisataData[wisataIndex].latitude,
      wisataData[wisataIndex].longitude,
    ];
    const bounds = L.latLngBounds([start, end]);
    map.fitBounds(bounds.pad(0.2));
  });

  // Generate recommendations
  $("#btn-recommend").click(function () {
    if (wisataData.length === 0 || perengkinganCriteria.length === 0) {
      alert("Data wisata atau kriteria perengkingan belum dimuat!");
      return;
    }

    if (!userLocation) {
      alert("Atur lokasi Anda terlebih dahulu!");
      return;
    }

    // Define weights for criteria (adjust as needed)
    const weights = cloud.get("kriteria-perengkingan");

    // The user location is the last index in distanceMatrix
    const userLocationIndex = wisataData.length;
    recommendations = weightedProductRanking(
      wisataData,
      weights,
      userLocationIndex,
      distanceMatrix.distances
    );
    const clusterNames = ["Tinggi", "Sedang", "Rendah"];

    // Show recommendations with detailed calculations
    showRecommendations(recommendations, weights);

    // Plot recommendations on map
    plotRecommendationsOnMap(recommendations);
  });

  // Weighted Product ranking with detailed steps
  function weightedProductRanking(data, weights, userLocationIndex, distances) {
    // Normalize weights (sum to 1)
    const totalWeight = weights.reduce((sum, w) => sum + w.weight, 0);
    const normalizedWeights = weights.map((w) => ({
      ...w,
      normalizedWeight: w.weight / totalWeight,
    }));

    // Calculate WP score for each alternative
    const rankedData = data.map((item, index) => {
      let score = 1;
      let calculationSteps = {};

      item.nilai_kriteria_perengkingan.forEach((nilai, idx) => {
        const crit = normalizedWeights.find(
          (w) => w.kode === nilai.kriteria_perengkingan_kode
        );
        if (crit) {
          const value = parseFloat(nilai.nilai);
          // For cost criteria, we use negative exponent
          const exponent = crit.benefit
            ? crit.normalizedWeight
            : -crit.normalizedWeight;
          const term = Math.pow(value, exponent);
          score *= term;

          calculationSteps[crit.kode] = {
            value: value,
            exponent: exponent,
            term: term,
            benefit: crit.benefit,
          };
        }
      });

      // Include distance as an additional criterion (lower distance is better)
      const distanceWeight = 0.2; // Adjust as needed
      let distanceTerm = 1;
      if (distances && userLocationIndex !== null) {
        const distance = distances[userLocationIndex][index];
        distanceTerm = Math.pow(1 / (distance + 0.001), distanceWeight);
        score *= distanceTerm;

        calculationSteps.distance = {
          value: distance,
          exponent: distanceWeight,
          term: distanceTerm,
          benefit: false,
        };
      }

      return {
        ...item,
        wpScore: score,
        distance:
          distances && userLocationIndex !== null
            ? distances[userLocationIndex][index]
            : null,
        calculationSteps: calculationSteps,
      };
    });

    // Sort by WP score descending
    rankedData.sort((a, b) => b.wpScore - a.wpScore);

    return rankedData.map((item, index) => ({
      ...item,
      rank: index + 1,
      calculationWeights: weights, // Add this line to store weights with each recommendation
    }));
  }

  // Show recommendations with detailed calculations
  function showRecommendations(recommendations, weights) {
    const clusterNames = ["Tinggi", "Sedang", "Rendah"];

    // Show recommendations
    let html =
      '<h4>Rekomendasi Wisata</h4><table class="table table-bordered"><thead><tr><th>Rank</th><th>Wisata</th><th>Klaster</th><th>Jarak (km)</th>';

    weights.forEach((crit) => {
      html += `<th>${crit.nama}</th>`;
    });

    html += "<th>Skor WP</th><th>Detail Perhitungan</th></tr></thead><tbody>";

    recommendations.forEach((item) => {
      html += `<tr>
        <td>${item.rank}</td>
        <td>${item.nama}</td>
        <td>${clusterNames[parseInt(item.klaster)]}</td>
        <td>${item.distance ? item.distance.toFixed(2) : "-"}</td>`;

      // Get criteria values in order
      const critValues = {};
      item.nilai_kriteria_perengkingan.forEach((nilai) => {
        critValues[nilai.kriteria_perengkingan_kode] = nilai.nilai;
      });

      weights.forEach((crit) => {
        html += `<td>${critValues[crit.kode] || "-"}</td>`;
      });

      html += `<td>${item.wpScore.toFixed(4)}</td>`;
      html += `<td><button class="btn btn-sm btn-info btn-show-calculation" data-id="${item.kode}">Lihat</button></td>`;
      html += "</tr>";
    });

    html += "</tbody></table>";
    $("#recommendation-results").html(html);

    // Add button to export to PDF
    html =
      '<button id="btn-export-pdf" class="btn btn-primary mt-3">Export ke PDF</button>';
    $("#recommendation-results").append(html);

    // Show WP calculation steps
    showWPCalculationSteps(recommendations, weights);
  }

  // Show detailed WP calculation steps
  function showWPCalculationSteps(recommendations, weights) {
    let html = "<h4>Perhitungan Detail Weighted Product</h4>";

    // Show weight normalization
    html += "<h5>Normalisasi Bobot Kriteria</h5>";
    html +=
      '<table class="table table-bordered"><thead><tr><th>Kriteria</th><th>Bobot Awal</th><th>Bobot Normalisasi</th><th>Jenis</th></tr></thead><tbody>';

    const totalWeight = weights.reduce((sum, w) => sum + w.weight, 0);
    weights.forEach((w) => {
      html += `<tr>
        <td>${w.nama}</td>
        <td>${w.weight}</td>
        <td>${(w.weight / totalWeight).toFixed(4)}</td>
        <td>${w.benefit ? "Benefit" : "Cost"}</td>
      </tr>`;
    });

    html += "</tbody></table>";

    // Show calculation formula
    html += "<h5>Rumus Perhitungan</h5>";
    html +=
      "<p>Skor WP = ∏(nilai kriteria<sup>bobot</sup>) untuk kriteria benefit</p>";
    html +=
      "<p>Skor WP = ∏(nilai kriteria<sup>-bobot</sup>) untuk kriteria cost</p>";
    html += "<p>Skor akhir termasuk jarak: ∏(1/distance<sup>0.2</sup>)</p>";

    $("#wp-calculation").html(html);
  }

  // Show detailed calculation for a specific recommendation
  $(document).on("click", ".btn-show-calculation", function () {
    const wisataKode = $(this).data("id");
    const recommendation = recommendations.find((r) => r.kode === wisataKode);

    if (!recommendation) return;

    // Use the weights stored with the recommendation
    const weights =
      recommendation.calculationWeights || cloud.get("kriteria-perengkingan");

    // Calculate total weight for normalization
    const totalWeight = weights.reduce((sum, w) => sum + w.weight, 0);

    if (!recommendation) return;

    let html = `<h4>Detail Perhitungan untuk ${recommendation.nama}</h4>`;
    html += "<p><strong>Rank:</strong> " + recommendation.rank + "</p>";
    html +=
      "<p><strong>Klaster:</strong> " +
      getClusterName(recommendation.klaster) +
      "</p>";
    if (recommendation.distance) {
      html +=
        "<p><strong>Jarak:</strong> " +
        recommendation.distance.toFixed(2) +
        " km</p>";
    }

    html += "<h5>Perhitungan Kriteria:</h5>";
    html +=
      '<table class="striped"><thead><tr><th>Kriteria</th><th>Nilai</th><th>Bobot</th><th>Jenis</th><th>Hasil</th></tr></thead><tbody>';

    // Get all criteria values
    const critValues = {};
    recommendation.nilai_kriteria_perengkingan.forEach((nilai) => {
      critValues[nilai.kriteria_perengkingan_kode] = nilai.nilai;
    });

    // Show calculation for each criteria
    perengkinganCriteria.forEach((crit) => {
      const value = critValues[crit.kode] || 0;
      const weightObj = weights.find((w) => w.kode === crit.kode);
      const weight = weightObj ? weightObj.weight : 0;
      const normalizedWeight = weight / totalWeight;
      const isBenefit = weightObj ? weightObj.benefit : true;
      const exponent = isBenefit ? normalizedWeight : -normalizedWeight;
      const term = Math.pow(value, exponent);

      html += `<tr>
            <td>${crit.nama}</td>
            <td>${value}</td>
            <td>${normalizedWeight.toFixed(4)}</td>
            <td>${isBenefit ? "Benefit" : "Cost"}</td>
            <td>${value}<sup>${exponent.toFixed(4)}</sup> = ${term.toFixed(
        4
      )}</td>
        </tr>`;
    });

    // Show distance calculation if available
    if (recommendation.distance) {
      const distanceWeight = 0.2;
      const distanceTerm = Math.pow(
        1 / (recommendation.distance + 0.001),
        distanceWeight
      );
      html += `<tr>
            <td>Jarak</td>
            <td>${recommendation.distance.toFixed(2)} km</td>
            <td>${distanceWeight}</td>
            <td>Cost</td>
            <td>(1/${recommendation.distance.toFixed(
              2
            )})<sup>${distanceWeight}</sup> = ${distanceTerm.toFixed(4)}</td>
        </tr>`;
    }

    html += "</tbody></table>";
    html += `<p><strong>Skor WP Akhir:</strong> ${recommendation.wpScore.toFixed(
      4
    )}</p>`;
    html += `<p><strong>Keterangan:</strong> Skor dihitung dengan mengalikan semua hasil perhitungan kriteria</p>`;

    // Show modal with detailed calculation
    $("#calculation-modal .modal-body").html(html);
    M.Modal.getInstance($("#calculation-modal")).open();
  });

  // Plot recommendations on map
  function plotRecommendationsOnMap(recommendations) {
    // Clear existing markers
    markers.forEach((marker) => map.removeLayer(marker));
    markers = [];

    // Add user marker if exists
    if (userMarker) {
      map.addLayer(userMarker);
    }

    // Add wisata markers with rank indicators
    recommendations.forEach((item) => {
      const baseUrlalItem = wisataData.find((w) => w.kode === item.kode);
      const marker = L.marker(
        [
          parseFloat(baseUrlalItem.latitude),
          parseFloat(baseUrlalItem.longitude),
        ],
        {
          icon: L.divIcon({
            className: "rank-icon",
            html: `<div style="background-color: ${getClusterColor(
              baseUrlalItem.klaster
            )}; 
                 width: 24px; height: 24px; border-radius: 50%; 
                 display: flex; align-items: center; justify-content: center;
                 color: white; font-weight: bold;">${item.rank}</div>`,
            iconSize: [24, 24],
          }),
          riseOnHover: true,
        }
      ).addTo(map).bindPopup(`<b>${baseUrlalItem.nama}</b><br>
                   Rank: ${item.rank}<br>
                   Klaster: ${getClusterName(baseUrlalItem.klaster)}<br>
                   Jarak: ${
                     item.distance ? item.distance.toFixed(2) + " km" : "-"
                   }<br>
                   Skor: ${item.wpScore.toFixed(4)}`);

      markers.push(marker);
    });

    // Fit bounds to show all markers
    const markerGroup = L.featureGroup(markers);
    if (userMarker) {
      markerGroup.addLayer(userMarker);
    }
    map.fitBounds(markerGroup.getBounds().pad(0.1));
  }

  // Export to PDF
  $(document).on("click", "#btn-export-pdf", function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Add title
    doc.setFontSize(18);
    doc.text("Laporan Rekomendasi Wisata", 105, 15, { align: "center" });
    doc.setFontSize(12);
    doc.text(`Tanggal: ${new Date().toLocaleDateString()}`, 105, 22, {
      align: "center",
    });

    // Add user location if available
    if (userLocation) {
      doc.text(
        `Lokasi Pengguna: ${userLocation.latitude}, ${userLocation.longitude}`,
        14,
        30
      );
      doc.text(`Nama Lokasi: ${$("#location-name").val() || "-"}`, 14, 36);
    }

    // Add recommendations table
    let y = 45;
    doc.setFontSize(14);
    doc.text("Daftar Rekomendasi Wisata", 14, y);
    y += 10;

    // Table headers
    const headers = [
      "Rank",
      "Wisata",
      "Klaster",
      "Jarak (km)",
      ...perengkinganCriteria.map((c) => c.nama),
      "Skor WP",
    ];
    const columnWidths = [
      15,
      40,
      20,
      20,
      ...Array(perengkinganCriteria.length).fill(20),
      25,
    ];

    // Draw table headers
    doc.setFontSize(10);
    doc.setFont(undefined, "bold");
    let x = 10;
    headers.forEach((header, i) => {
      doc.text(header, x, y);
      x += columnWidths[i];
    });
    y += 6;

    // Draw line
    doc.line(10, y, 190, y);
    y += 4;

    // Get recommendations data
    const weights = cloud.get("kriteria-perengkingan");

    const recommendations = weightedProductRanking(
      wisataData,
      weights,
      wisataData.length,
      distanceMatrix.distances
    );

    const clusterNames = ["Tinggi", "Sedang", "Rendah"];

    // Add table rows
    doc.setFont(undefined, "normal");
    recommendations.forEach((item) => {
      if (y > 270) {
        // Add new page if needed
        doc.addPage();
        y = 20;
      }

      x = 10;

      // Rank
      doc.text(item.rank.toString(), x, y);
      x += columnWidths[0];

      // Wisata name
      doc.text(item.nama, x, y, { maxWidth: 35 });
      x += columnWidths[1];

      // Cluster
      doc.text(clusterNames[parseInt(item.klaster)], x, y);
      x += columnWidths[2];

      // Distance
      doc.text(item.distance ? item.distance.toFixed(2) : "-", x, y);
      x += columnWidths[3];

      // Criteria values
      const critValues = {};
      item.nilai_kriteria_perengkingan.forEach((nilai) => {
        critValues[nilai.kriteria_perengkingan_kode] = nilai.nilai;
      });

      perengkinganCriteria.forEach((crit) => {
        doc.text(critValues[crit.kode] || "-", x, y);
        x += columnWidths[4 + perengkinganCriteria.indexOf(crit)];
      });

      // WP Score
      doc.text(item.wpScore.toFixed(4), x, y);

      y += 6;
    });

    // Add calculation details
    doc.addPage();
    y = 20;
    doc.setFontSize(14);
    doc.text("Detail Perhitungan Weighted Product", 105, y, {
      align: "center",
    });
    y += 10;

    // Weight normalization
    doc.setFontSize(12);
    doc.text("Normalisasi Bobot Kriteria:", 14, y);
    y += 7;

    const totalWeight = weights.reduce((sum, w) => sum + w.weight, 0);
    weights.forEach((w) => {
      doc.text(
        `${w.nama}: ${w.weight} → ${(w.weight / totalWeight).toFixed(4)} (${
          w.benefit ? "Benefit" : "Cost"
        })`,
        20,
        y
      );
      y += 6;
    });

    // Example calculation
    y += 6;
    doc.setFontSize(12);
    doc.text("Contoh Perhitungan untuk Rekomendasi Teratas:", 14, y);
    y += 7;

    if (recommendations.length > 0) {
      const topItem = recommendations[0];
      doc.text(`Wisata: ${topItem.nama}`, 20, y);
      y += 6;
      doc.text(`Skor WP: ${topItem.wpScore.toFixed(4)}`, 20, y);
      y += 6;

      for (const [kode, calc] of Object.entries(topItem.calculationSteps)) {
        if (kode === "distance") {
          doc.text(
            `Jarak: ${calc.value.toFixed(2)} → (1/${calc.value.toFixed(2)})^${
              calc.exponent
            } = ${calc.term.toFixed(4)}`,
            20,
            y
          );
        } else {
          const crit = perengkinganCriteria.find((c) => c.kode === kode);
          doc.text(
            `${crit?.nama || kode}: ${calc.value} → ${calc.value}^${
              calc.exponent
            } = ${calc.term.toFixed(4)}`,
            20,
            y
          );
        }
        y += 6;
      }
    }

    // Save the PDF
    doc.save("rekomendasi_wisata.pdf");
  });

  // Show accuracy chart
  // Initialize accuracy chart when accuracy tab is clicked
  // Pindahkan ini ke bagian inisialisasi
  $(document).on("click", 'a[href="#accuracy-tab"]', function () {
    if (clusterResults && clusterResults.silhouetteScore !== undefined) {
      showAccuracyChart();
    } else {
      $("#accuracy-tab").html(`
            <div class="card-panel yellow lighten-4">
                <p>Silakan lakukan klasterisasi terlebih dahulu untuk melihat nilai akurasi</p>
                <a class="waves-effect waves-light btn" id="btn-cluster-from-accuracy">
                    Lakukan Klasterisasi
                </a>
            </div>
        `);
    }
  });

  // Tambahkan handler untuk tombol klasterisasi dari tab akurasi
  $(document).on("click", "#btn-cluster-from-accuracy", function () {
    $("#btn-cluster").click();
    M.Tabs.getInstance($(".tabs")).select("cluster-tab");
  });

  // Function to show accuracy chart
  function showAccuracyChart() {
    // Hapus chart sebelumnya jika ada
    const oldCanvas = document.getElementById("accuracyChart");
    if (oldCanvas) {
      oldCanvas.remove();
    }

    // Buat elemen canvas baru
    $("#accuracy-tab").html(
      '<canvas id="accuracyChart" height="300"></canvas>'
    );

    const ctx = document.getElementById("accuracyChart").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["K-Means Clustering"],
        datasets: [
          {
            label: "Silhouette Score",
            data: [clusterResults.silhouetteScore],
            backgroundColor: "rgba(54, 162, 235, 0.5)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            max: 1,
            ticks: {
              stepSize: 0.1,
            },
          },
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function (context) {
                return `Score: ${context.raw.toFixed(
                  4
                )} (${interpretSilhouetteScore(context.raw)})`;
              },
            },
          },
          legend: {
            display: false,
          },
        },
      },
    });

    // Tambahkan interpretasi
    const interpretation = interpretSilhouetteScore(
      clusterResults.silhouetteScore
    );
    $("#accuracy-tab").prepend(`
        <div class="card-panel blue lighten-5">
            <h5>Hasil Akurasi Klasterisasi</h5>
            <p>Silhouette Score: <strong>${clusterResults.silhouetteScore.toFixed(
              4
            )}</strong></p>
            <p>Interpretasi: <strong>${interpretation}</strong></p>
        </div>
    `);
  }
});
