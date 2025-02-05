<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Bin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            color: #333;
            text-align: center;
            padding: 20px;
        }

        header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        main {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        #map-container {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
        }

        th, td {
            padding: 15px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #28a745;
            color: white;
            font-size: 1.1em;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .full-bin {
            background-color: #dc3545 !important;
            color: white;
            font-weight: bold;
        }

        .timestamp {
            color: #fff700;
            font-weight: bold;
        }
    </style>

    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body>
    <header>
        <h1>Smart Bin Management System</h1>
    </header>
    <main>
        <section>
            <h2>Smart Bin Locations</h2>
            <div id="map-container"></div>
        </section>
        <section>
            <h2>Bin Status</h2>
            <table>
                <thead>
                    <tr>
                        <th>Bin ID</th>
                        <th>Location</th>
                        <th>Fill Level (%)</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody id="bin-data">
                    <!-- Data will be inserted here dynamically -->
                </tbody>
            </table>
        </section>
    </main>
    <script>
        // Initialize Leaflet map
        var map = L.map('map-container', {
            center: [20.5937, 78.9629], // Default center (India)
            zoom: 5,
            maxBounds: [[-90, -180], [90, 180]], // Prevents map repetition
            maxZoom: 18,
            minZoom: 3
        });

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Dictionary to store bin markers
        var binMarkers = {};

        // Function to update bin data and map markers
        function updateBinData(binId, location, fillLevel) {
            const tableBody = document.getElementById("bin-data");
            let row = document.getElementById(`bin-${binId}`);
            let timestamp = new Date().toLocaleString();

            if (!row) {
                row = document.createElement("tr");
                row.id = `bin-${binId}`;
                row.innerHTML = `
                    <td>${binId}</td>
                    <td>${location}</td>
                    <td>${fillLevel}%</td>
                    <td class="timestamp"></td>`;
                tableBody.appendChild(row);
            } else {
                row.cells[2].textContent = `${fillLevel}%`;
            }

            // Change row color and display timestamp if bin is full
            if (fillLevel === 100) {
                row.classList.add("full-bin");
                row.cells[3].textContent = `Full at ${timestamp}`;
            } else {
                row.classList.remove("full-bin");
                row.cells[3].textContent = '';
            }

            // Update map marker
            var latLon = location.split(",");
            var lat = parseFloat(latLon[0]);
            var lon = parseFloat(latLon[1]);

            if (binMarkers[binId]) {
                binMarkers[binId].setLatLng([lat, lon]);
                binMarkers[binId].setPopupContent(`<b>Bin ${binId}</b><br>Status: ${fillLevel}%`);
            } else {
                var marker = L.marker([lat, lon]).addTo(map)
                    .bindPopup(`<b>Bin ${binId}</b><br>Status: ${fillLevel}%`);
                binMarkers[binId] = marker;
            }

            // Zoom into bin location
            map.setView([lat, lon], 14);
        }

        // Function to send data via console
        window.postBinData = function(data) {
            updateBinData(data.binId, data.location, data.fillLevel);
        };
    </script>
</body>
</html>
