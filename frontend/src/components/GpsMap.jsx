import React, { useEffect, useRef, useState } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix Leaflet default icon URLs in bundlers
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

export default function GpsMap({
  lat = -6.289123,
  lng = 106.918456,
  title = 'Titik Koordinat',
  subtitle = '',
  height = '200px',
  zoom = 15,
  interactive = false,
  showSearch = false,
  onChange = null,
  markers = [],
  className = '',
  defaultLayer = 'google_streets', // 'google_streets' | 'google_satellite' | 'carto'
}) {
  const mapContainerRef = useRef(null);
  const mapInstanceRef = useRef(null);
  const markerRef = useRef(null);

  const [searchQuery, setSearchQuery] = useState('');
  const [searching, setSearching] = useState(false);
  const [currentCoords, setCurrentCoords] = useState({
    lat: Number(lat) && !isNaN(Number(lat)) ? Number(lat) : -6.289123,
    lng: Number(lng) && !isNaN(Number(lng)) ? Number(lng) : 106.918456,
  });
  const [searchResults, setSearchResults] = useState([]);
  const [copied, setCopied] = useState(false);

  // Synchronize internal state when prop changes
  useEffect(() => {
    const validLat = Number(lat) && !isNaN(Number(lat)) ? Number(lat) : -6.289123;
    const validLng = Number(lng) && !isNaN(Number(lng)) ? Number(lng) : 106.918456;
    setCurrentCoords({ lat: validLat, lng: validLng });
    if (markerRef.current) {
      markerRef.current.setLatLng([validLat, validLng]);
    }
  }, [lat, lng]);

  const updatePosition = (newLat, newLng, moveMap = true, zoomLevel = null) => {
    const roundedLat = Number(newLat.toFixed(6));
    const roundedLng = Number(newLng.toFixed(6));

    setCurrentCoords({ lat: roundedLat, lng: roundedLng });

    if (markerRef.current) {
      markerRef.current.setLatLng([roundedLat, roundedLng]);
      markerRef.current.setPopupContent(`
        <div style="font-family: inherit; font-size: 11px; line-height: 1.35; min-width: 130px; text-align: center; padding: 3px;">
          <strong style="display: block; color: #0f172a; font-size: 12px; margin-bottom: 2px;">${title}</strong>
          ${subtitle ? `<span style="color: #64748b; font-family: monospace; font-size: 10px; display: block;">${subtitle}</span>` : ''}
          <span style="color: #dc2626; font-family: monospace; font-weight: bold; font-size: 10px; display: block; margin-top: 3px;">${roundedLat}, ${roundedLng}</span>
          <a href="https://www.google.com/maps?q=${roundedLat},${roundedLng}" target="_blank" rel="noreferrer" style="display: inline-block; margin-top: 4px; padding: 2px 8px; background: #2563eb; color: #ffffff; border-radius: 4px; text-decoration: none; font-size: 9.5px; font-weight: bold;">
            Buka Google Maps ↗
          </a>
        </div>
      `).openPopup();
    }

    if (moveMap && mapInstanceRef.current) {
      if (zoomLevel) {
        mapInstanceRef.current.setView([roundedLat, roundedLng], zoomLevel);
      } else {
        mapInstanceRef.current.panTo([roundedLat, roundedLng]);
      }
    }

    if (onChange) {
      onChange(roundedLat, roundedLng);
    }
  };

  useEffect(() => {
    if (!mapContainerRef.current) return;

    if (mapInstanceRef.current) {
      mapInstanceRef.current.remove();
      mapInstanceRef.current = null;
    }

    try {
      const map = L.map(mapContainerRef.current, {
        center: [currentCoords.lat, currentCoords.lng],
        zoom: zoom,
        zoomControl: true,
        scrollWheelZoom: interactive,
        dragging: true,
        attributionControl: false,
      });

      mapInstanceRef.current = map;

      // 1. Google Maps Streets / Roadmap
      const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Google Maps',
      });

      // 2. Google Maps Satellite Hybrid (Satellite + Roads & Labels)
      const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Google Satellite',
      });

      // 3. Google Maps Terrain
      const googleTerrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Google Terrain',
      });

      // 4. CartoDB Voyager Tile Layer
      const cartoVoyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd',
        attribution: 'CartoDB',
      });

      if (defaultLayer === 'google_satellite') {
        googleHybrid.addTo(map);
      } else if (defaultLayer === 'carto') {
        cartoVoyager.addTo(map);
      } else {
        googleStreets.addTo(map);
      }

      // Add Base Layer Switcher Control
      const baseMaps = {
        '🗺️ Google Maps (Streets)': googleStreets,
        '🛰️ Google Satellite (Hybrid)': googleHybrid,
        '⛰️ Google Terrain': googleTerrain,
        '🏙️ CartoDB Voyager': cartoVoyager,
      };

      L.control.layers(baseMaps, null, { position: 'topright', collapsed: true }).addTo(map);

      // Custom Pulsing Pin Marker Icon
      const customIcon = L.divIcon({
        className: 'custom-gps-pin',
        html: `
          <div style="position: relative; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <div style="position: absolute; width: 32px; height: 32px; background: rgba(220, 38, 38, 0.3); border-radius: 50%; animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
            <div style="width: 24px; height: 24px; background: #dc2626; border: 2.5px solid #ffffff; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.35); display: flex; align-items: center; justify-content: center; z-index: 2;">
              <div style="width: 7px; height: 7px; background: #ffffff; border-radius: 50%;"></div>
            </div>
          </div>
        `,
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        popupAnchor: [0, -18],
      });

      const mainMarker = L.marker([currentCoords.lat, currentCoords.lng], {
        icon: customIcon,
        draggable: interactive,
      }).addTo(map);

      markerRef.current = mainMarker;

      if (title) {
        const popupContent = `
          <div style="font-family: inherit; font-size: 11px; line-height: 1.35; min-width: 130px; text-align: center; padding: 3px;">
            <strong style="display: block; color: #0f172a; font-size: 12px; margin-bottom: 2px;">${title}</strong>
            ${subtitle ? `<span style="color: #64748b; font-family: monospace; font-size: 10px; display: block;">${subtitle}</span>` : ''}
            <span style="color: #dc2626; font-family: monospace; font-weight: bold; font-size: 10px; display: block; margin-top: 3px;">${currentCoords.lat}, ${currentCoords.lng}</span>
            <a href="https://www.google.com/maps?q=${currentCoords.lat},${currentCoords.lng}" target="_blank" rel="noreferrer" style="display: inline-block; margin-top: 4px; padding: 2px 8px; background: #2563eb; color: #ffffff; border-radius: 4px; text-decoration: none; font-size: 9.5px; font-weight: bold;">
              Buka Google Maps ↗
            </a>
          </div>
        `;
        mainMarker.bindPopup(popupContent).openPopup();
      }

      // 1. Direct Click Anywhere on Map to Pick Location
      map.on('click', (e) => {
        if (interactive) {
          updatePosition(e.latlng.lat, e.latlng.lng, false);
        }
      });

      // 2. Drag Marker to Pick Location
      if (interactive) {
        mainMarker.on('dragend', (e) => {
          const newPos = e.target.getLatLng();
          updatePosition(newPos.lat, newPos.lng, false);
        });
      }

      // Additional markers
      if (markers && markers.length > 0) {
        markers.forEach((m) => {
          if (m.lat && m.lng) {
            const mIcon = L.divIcon({
              className: 'custom-sub-pin',
              html: `
                <div style="width: 16px; height: 16px; background: ${m.color || '#2563eb'}; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;">
                  <div style="width: 4px; height: 4px; background: white; border-radius: 50%;"></div>
                </div>
              `,
              iconSize: [16, 16],
              iconAnchor: [8, 8],
            });

            L.marker([m.lat, m.lng], { icon: mIcon })
              .bindPopup(`
                <div style="font-size: 11px; text-align: center;">
                  <strong>${m.title || 'Titik Jaringan'}</strong>
                  ${m.desc ? `<div style="font-size: 10px; color: #64748b;">${m.desc}</div>` : ''}
                </div>
              `)
              .addTo(map);
          }
        });
      }

      setTimeout(() => {
        if (mapInstanceRef.current) {
          mapInstanceRef.current.invalidateSize();
        }
      }, 250);

    } catch (err) {
      console.error('Leaflet Google Maps Initialization Error:', err);
    }

    return () => {
      if (mapInstanceRef.current) {
        mapInstanceRef.current.remove();
        mapInstanceRef.current = null;
      }
    };
  }, [interactive, title, subtitle, zoom, defaultLayer]);

  // Handle Search by Coordinate (e.g. "-6.2891, 106.9184") or by Place Name (e.g. "Jatiwaringin Bekasi")
  const handleSearchSubmit = async (e) => {
    if (e) e.preventDefault();
    const query = searchQuery.trim();
    if (!query) return;

    // A. Check if query is raw coordinates (Lat, Lng)
    const coordMatch = query.match(/^([-+]?\d+(\.\d+)?)[,\s]+([-+]?\d+(\.\d+)?)$/);
    if (coordMatch) {
      const parsedLat = parseFloat(coordMatch[1]);
      const parsedLng = parseFloat(coordMatch[3]);
      if (!isNaN(parsedLat) && !isNaN(parsedLng)) {
        updatePosition(parsedLat, parsedLng, true, 16);
        setSearchResults([]);
        return;
      }
    }

    // B. Search via OpenStreetMap / Google Geocoding Search
    setSearching(true);
    try {
      const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=5`);
      const data = await res.json();
      if (data && data.length > 0) {
        setSearchResults(data);
        const top = data[0];
        updatePosition(parseFloat(top.lat), parseFloat(top.lon), true, 16);
      } else {
        alert(`Lokasi "${query}" tidak ditemukan. Coba ketik nama jalan, kelurahan, atau format koordinat "-6.xxxx, 106.xxxx"`);
      }
    } catch {
      alert('Gagal mencari lokasi. Pastikan koneksi internet aktif.');
    } finally {
      setSearching(false);
    }
  };

  // Get Current User GPS Location via Browser Geolocation API
  const handleGetCurrentLocation = () => {
    if (!navigator.geolocation) {
      alert('Browser tidak mendukung Geolocation.');
      return;
    }

    setSearching(true);
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        setSearching(false);
        updatePosition(pos.coords.latitude, pos.coords.longitude, true, 17);
      },
      (err) => {
        setSearching(false);
        alert(`Gagal mendeteksi lokasi GPS: ${err.message}`);
      },
      { enableHighAccuracy: true, timeout: 10000 }
    );
  };

  const handleCopyCoords = () => {
    const text = `${currentCoords.lat}, ${currentCoords.lng}`;
    navigator.clipboard.writeText(text);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className={`space-y-2 ${className}`}>
      {/* Search Bar & GPS Controls */}
      {showSearch && (
        <div className="relative z-10">
          <form onSubmit={handleSearchSubmit} className="flex gap-1.5 items-center">
            <div className="relative flex-1">
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Cari lokasi / alamat atau masukkan koordinat (-6.2891, 106.9184)..."
                className="w-full bg-white border border-slate-300 rounded-lg py-1.5 pl-8 pr-3 text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 shadow-sm transition"
              />
              <i className="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <button
              type="submit"
              disabled={searching}
              className="bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 shrink-0 cursor-pointer shadow transition"
            >
              {searching ? (
                <i className="fa-solid fa-spinner fa-spin"></i>
              ) : (
                <i className="fa-solid fa-location-arrow"></i>
              )}
              <span>Cari</span>
            </button>

            <button
              type="button"
              onClick={handleGetCurrentLocation}
              title="Kunci ke Titik GPS Saya Saat Ini"
              className="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 font-bold px-2.5 py-1.5 rounded-lg text-xs flex items-center gap-1 shrink-0 cursor-pointer shadow-sm transition"
            >
              <i className="fa-solid fa-crosshairs text-red-600"></i>
              <span className="hidden sm:inline">Lokasi Saya</span>
            </button>
          </form>

          {/* Search suggestions dropdown */}
          {searchResults.length > 1 && (
            <div className="absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl overflow-hidden z-50 text-xs divide-y divide-slate-100 max-h-48 overflow-y-auto">
              {searchResults.map((item, idx) => (
                <button
                  key={idx}
                  type="button"
                  onClick={() => {
                    updatePosition(parseFloat(item.lat), parseFloat(item.lon), true, 16);
                    setSearchResults([]);
                    setSearchQuery(item.display_name.split(',')[0]);
                  }}
                  className="w-full text-left p-2 hover:bg-slate-50 flex items-start gap-2 text-slate-700 transition"
                >
                  <i className="fa-solid fa-location-dot text-red-500 mt-0.5 shrink-0"></i>
                  <span className="truncate">{item.display_name}</span>
                </button>
              ))}
            </div>
          )}
        </div>
      )}

      {/* Leaflet Map Canvas */}
      <div className="relative rounded-xl border border-slate-300 shadow-inner overflow-hidden">
        <div
          ref={mapContainerRef}
          style={{ height: height, width: '100%' }}
          className="z-0"
        />

        {/* Floating Hint Overlay on Map */}
        {interactive && (
          <div className="absolute bottom-2 left-2 bg-slate-900/80 backdrop-blur-sm text-white px-2.5 py-1 rounded-md text-[10px] font-medium flex items-center gap-1 z-[400] pointer-events-none shadow">
            <i className="fa-solid fa-hand-pointer text-amber-400"></i>
            <span>Klik atau geser pin langsung pada peta untuk mengubah titik</span>
          </div>
        )}
      </div>

      {/* Coordinate Indicator Bar */}
      <div className="flex flex-wrap justify-between items-center bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg text-xs">
        <div className="flex items-center gap-2">
          <span className="text-slate-400 font-semibold text-[11px]">Koordinat Terpilih:</span>
          <span className="font-mono font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-200 text-[11px]">
            {currentCoords.lat}, {currentCoords.lng}
          </span>
        </div>

        <div className="flex items-center gap-1.5">
          <button
            type="button"
            onClick={handleCopyCoords}
            className="text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 cursor-pointer transition"
          >
            <i className={`fa-solid ${copied ? 'fa-check text-emerald-600' : 'fa-copy'}`}></i>
            <span>{copied ? 'Tersalin!' : 'Salin Koordinat'}</span>
          </button>
          <a
            href={`https://www.google.com/maps?q=${currentCoords.lat},${currentCoords.lng}`}
            target="_blank"
            rel="noreferrer"
            className="text-blue-600 hover:text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 cursor-pointer transition"
          >
            <i className="fa-solid fa-arrow-up-right-from-square"></i>
            <span>Buka Google Maps</span>
          </a>
        </div>
      </div>
    </div>
  );
}
