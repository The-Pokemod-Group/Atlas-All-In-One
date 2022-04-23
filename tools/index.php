<?php
error_reporting(0);

include('config/config.php');

if ($_POST['data']) { map_helper_init(); } else { ?><!DOCTYPE html>
<html>
  <head>
    <title>RDM-Tools</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
      }
      #map {
        height: 100%;
      }
      .modal-loader .modal-dialog{
        display: table;
        position: relative;
        margin: 0 auto;
        top: calc(50% - 24px);
      }
      .modal-loader .modal-dialog .modal-content{
        background-color: transparent;
        border: none;
      }
      .nestName {
        min-width: 175px;
        text-align: center;
        font-weight: bold;
      }
      .buttonOff {
        background: #ccc;
      }
      .easy-button-container.disabled, .easy-button-button.disabled {
        display: none;
      }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.4/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-easybutton@2.0.0/src/easy-button.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-toolbar@0.4.0-alpha.1/dist/leaflet.toolbar.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.67.0/dist/L.Control.Locate.min.css" />
    <link rel="stylesheet" href="css/leaflet-search.css" />
    <link rel="stylesheet" href="css/pick-a-color-1.2.3.min.css">
    <link rel="stylesheet" href="css/multi.select.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.4/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Leaflet.EasyButton/2.3.0/easy-button.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-geometryutil@0.9.0/src/leaflet.geometryutil.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@5/turf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/osmtogeojson@3.0.0-beta.3/osmtogeojson.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-toolbar@0.4.0-alpha.1/dist/leaflet.toolbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/s2-geometry@1.2.10/src/s2geometry.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-path-drag@1.1.0/dist/L.Path.Drag.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.67.0/dist/L.Control.Locate.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="other/en.js"></script>
    <script type="text/javascript" src="other/de.js"></script>
    <script type="text/javascript" src="other/fr.js"></script>
    <script type="text/javascript" src="other/salesman.js"></script>
    <script type="text/javascript" src="other/leaflet-search.js"></script>
    <script src="other/tinycolor-0.9.15.min.js"></script>
    <script src="other/pick-a-color-1.2.3.min.js"></script>
    <script src="other/multi.select.js"></script>

<script type="text/javascript">
//map and control vars
let map;
let manualCircle = false;
let newPOI = false;
let csvImport = null;
let copyOutput = null;
let exportListCount = null;
let subs = enSubs;
let drawControl,
  buttonManualCircle,
  buttonImportNests,
  buttonModalImportPolygon,
  buttonModalImportSubmissions,
  buttonModalImportInstance,
  buttonModalNestOptions,
  buttonTrash,
  buttonTrashRoute,
  buttonGenerateRoute,
  buttonOptimizeRoute,
  buttonModalOutput,
  buttonMapModePoiViewer,
  buttonMapModeRouteGenerator,
  buttonShowGyms,
  buttonShowPokestops,
  buttonShowPokestopsRange,
  buttonShowSpawnpoints,
  buttonHideOldSpawnpoints,
  buttonShowUnknownPois,
  buttonShowMissingQuests,
  buttonSettingsModal,
  buttonClearSubs,
  buttonNewPOI,
  buttonShowRoute,
  barShowPolyOpts,
  barOutput,
  barWayfarer,
  barRightOpts,
  barMapMode;
//data vars
let gyms = [],
  pokestops = [],
  pokestoprange = [],
  spawnpoints = [],
  spawnpoints_u = [],
  instances = [],
  circleInstance = [],
  mySelect = [],
  myQuestSelect = [],
  spawnReport = [];
//options vars
let settings = {
  showGyms: null,
  showPokestops: null,
  showPokestopsRange: null,
  showSpawnpoints: null,
  showUnknownPois: null,
  hideOldSpawnpoints: null,
  showMissingQuests: false,
  showRoute: null,
  oldSpawnpointsTimestamp: null,
  circleSize: null,
  optimizationAttempts: null,
  nestMigrationDate: null,
  spawnReportLimit: null,
  mapMode: null,
  mapCenter: null,
  mapZoom: null,
  cellsLevel0: null,
  cellsLevel0Check: false,
  cellsLevel1: null,
  cellsLevel1Check: false,
  cellsLevel2: null,
  cellsLevel2Check: false,
  s2CountPOI: false,
  selectCircleRange: null,
  tlLink: null,
  tlChoice: null,
  language: null,
  generateWithS2Cells: false
};
//map layer vars
let gymLayer,
  pokestopLayer,
  pokestopRangeLayer,
  spawnpointLayer,
  editableLayer,
  admLayer,
  circleS2Layer,
  circleLayer,
  instanceLayer,
  questLayer,
  bgLayer,
  nestLayer,
  viewCellLayer,
  subsLayer,
  routingLayer,
  bootstrapLayer,
  exportList;
$(function(){
  loadSettings();
  getLanguage();
  initMap();
  map.keyboard.disable();
  setMapMode();
  setShowMode();
  $('#nestMigrationDate').datetimepicker('sideBySide', true)
  $('#oldSpawnpointsTimestamp').datetimepicker('sideBySide', true)
  $(".pick-a-color").pickAColor({
        showSpectrum          : false,
        showSavedColors       : false,
        saveColorsPerElement  : false,
        fadeMenuToggle        : false,
        showAdvanced          : true,
        showBasicColors       : true,
        showHexInput          : false,
        allowBlank            : false
  });
  $('#savePolygon').on('click', function(event) {
    let polygonData = [];
    let importReady = true;
    let importType = $("#importPolygonForm input[name=importPolygonDataType]:checked").val();
    if (importType == 'importPolygonDataTypeCoordList') {
      polygonData.push(csvtoarray($('#importPolygonData').val().trim()));
      importReady = true;
    } else if (importType == 'importPolygonDataTypeGeoJson') {
      let geoJson = JSON.parse($('#importPolygonData').val());
      if (geoJson.type == 'FeatureCollection') {
        let counter = 0;
        geoJson.features.forEach(function(feature) {          
          if (feature.type == 'Feature' && feature.geometry.type == 'Polygon' && importReady == true) {
            polygonData.push(turf.flip(feature).geometry.coordinates);
            polygonData[counter].id = feature.id ? feature.id : counter;
            polygonData[counter].name = feature.properties.name ? feature.properties.name : '';
            polygonData[counter].path = JSON.stringify(turf.flip(feature).geometry.coordinates);
            if (feature.properties.area_center_point != undefined) {
              polygonData[counter].centerLat = feature.properties.area_center_point.coordinates[1];
              polygonData[counter].centerLon = feature.properties.area_center_point.coordinates[0];
            }
            counter++;
            importReady = true;
          } else {
            importReady = false;
          }
        });
      } else {
        if (geoJson.type == 'Feature' && geoJson.geometry.type == 'Polygon') {
          polygonData.push(turf.flip(geoJson).geometry.coordinates);
          polygonData[0].id = geoJson.id ? geoJson.id : 0;
          polygonData[0].name = geoJson.properties.name ? geoJson.properties.name : '' ;
          polygonData[0].path = JSON.stringify(turf.flip(geoJson).geometry.coordinates);
          if (geoJson.properties.area_center_point != undefined) {
            polygonData[0].centerLat = geoJson.properties.area_center_point.coordinates[1];
            polygonData[0].centerLon = geoJson.properties.area_center_point.coordinates[0];
          }
          importReady = true;
        }
      }
    } else {
      importReady = false;
    }
    if (importReady = true) {
      polygonColor = $("#polygonColor input").val();
      let polygonOptions = {
        clickable: false,
        color: "#" + polygonColor,
        fill: true,
        fillColor: null,
        fillOpacity: 0.2,
        opacity: 0.5,
        stroke: true,
        weight: 4
      };
      polygonData.forEach(function(polygon) {
        let layer = L.polygon(polygon, polygonOptions).addTo(editableLayer);
        layer.tags.osmid = polygon.id;
        layer.tags.name = (polygon.name != 'Unknown Parkname') ? polygon.name : '';
        let area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
        let readableArea = L.GeometryUtil.readableArea(area, true);
        layer.tags.path = polygon.path;
        layer.tags.centerLat = polygon.centerLat ? polygon.centerLat : layer._bounds._northEast.lat-((layer._bounds._northEast.lat-layer._bounds._southWest.lat)/2);
        layer.tags.centerLon = polygon.centerLon ? polygon.centerLon : layer._bounds._northEast.lng-((layer._bounds._northEast.lng-layer._bounds._southWest.lng)/2);
        layer.bindPopup(function (layer) {
          if (layer.tags.name == '') {
            name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + subs.polygon + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
            nameInput = '<hr><div class="input-group mb-3">' +
                              '<div class="input-group-prepend">' +
                                '<span class="input-group-text">' + subs.name + '</span>' +
                              '</div>' +
                              '<input id="polygonName" name="polygonName" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="text" class="form-control" aria-label="Polygon name">' +
                            '</div>';
          } else {
            name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + layer.tags.name + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
            nameInput = '<hr>';        
          }
          if (layer.tags.included == true) {
            included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm removeFromExport" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeFromExport + '</span></div></div>';
          } else {
            included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm addToExport" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.addToExport + '</span></div></div>';
          }
          let merge = '';
          if (layer.tags.merged != true) {
            merge = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm mergePolygons" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.mergePolygons + '</span></div></div>';
          }
          let output = name +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm getSpawnReport" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.getSpawnReport + '</span></div></div>' +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeMap + '</span></div></div>' +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm exportLayer" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportPolygon + '</span></div></div>' +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm exportPoints" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportVP + '</span></div></div>' +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm countPoints" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.countVP + '</span></div></div>' +
                   nameInput + included + merge;
          return output;
        }, {maxWidth: 500, minWidth: 300});
      });
    }
    $('#modalImport').modal('hide');
  });
  $('#saveNestPolygon').on('click', function(event) {
    let polygonData = [];
    let importReady = true;
    let importType = $("#importPolygonForm input[name=importPolygonDataType]:checked").val();
    if (importType == 'importPolygonDataTypeCoordList') {
      polygonData.push(csvtoarray($('#importPolygonData').val().trim()));
      importReady = true;
    } else if (importType == 'importPolygonDataTypeGeoJson') {
      let geoJson = JSON.parse($('#importPolygonData').val());
      if (geoJson.type == 'FeatureCollection') {
        let counter = 0;
        geoJson.features.forEach(function(feature) {
          if (feature.type == 'Feature' && feature.geometry.type == 'Polygon' && importReady == true) {
            polygonData.push(turf.flip(feature).geometry.coordinates);
            polygonData[counter].id = feature.id ? feature.id : counter;
            polygonData[counter].name = feature.properties.name ? feature.properties.name : '';
            polygonData[counter].path = JSON.stringify(turf.flip(feature).geometry.coordinates);
            if (feature.properties.area_center_point != undefined) {
              polygonData[counter].centerLat = feature.properties.area_center_point.coordinates[1];
              polygonData[counter].centerLon = feature.properties.area_center_point.coordinates[0];
            }
            counter++;
            importReady = true;
          } else {
            importReady = false;
          }
        });
      } else if (geoJson.type == 'Feature' && geoJson.geometry.type == 'Polygon') {
          polygonData.push(turf.flip(geoJson).geometry.coordinates);
          polygonData[0].id = geoJson.id ? geoJson.id : 0;
          polygonData[0].name = geoJson.properties.name ? geoJson.properties.name : '' ;
          polygonData[0].path = JSON.stringify(turf.flip(geoJson).geometry.coordinates);
          if (geoJson.properties.area_center_point != undefined) {
            polygonData[0].centerLat = geoJson.properties.area_center_point.coordinates[1];
            polygonData[0].centerLon = geoJson.properties.area_center_point.coordinates[0];
          }
          importReady = true;
      }
    } else {
      importReady = false;
    }
    if (importReady = true) {
      let polygonOptions = {
        clickable: false,
        color: "#ff8833",
        fill: true,
        fillColor: null,
        fillOpacity: 0.2,
        opacity: 0.5,
        stroke: true,
        weight: 4
      }
      polygonData.forEach(function(polygon) {
        let layer = L.polygon(polygon, polygonOptions);
        layer.tags = {};
        layer.tags.osmid = polygon.id;
        layer.tags.name = (polygon.name != 'Unknown Parkname') ? polygon.name : '';
        let area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
        let readableArea = L.GeometryUtil.readableArea(area, true);
        layer.tags.path = polygon.path;
        layer.tags.centerLat = polygon.centerLat ? polygon.centerLat : layer._bounds._northEast.lat-((layer._bounds._northEast.lat-layer._bounds._southWest.lat)/2);
        layer.tags.centerLon = polygon.centerLon ? polygon.centerLon : layer._bounds._northEast.lng-((layer._bounds._northEast.lng-layer._bounds._southWest.lng)/2);
        layer.tags.included = false;
        let name = '';
        let nameInput = '';
        let included = '';
        layer.bindPopup(function (layer) {
            if (layer.tags.name != '') {
              name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + subs.nest + ': ' + layer.tags.name + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
              nameInput = '<hr>';
            } else {
              name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + subs.polygon + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
              nameInput = '<hr><div class="input-group mb-3">' +
                              '<div class="input-group-prepend">' +
                                '<span class="input-group-text">' + subs.name + '</span>' +
                              '</div>' +
                              '<input id="polygonName" name="polygonName" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="text" class="form-control" aria-label="Polygon name">' +
                            '</div>';
            }
            if (layer.tags.included == true) {
              included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm removeFromExport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeFromExport + '</span></div></div>';
            } else {
              included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm addToExport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.addToExport + '</span></div></div>';
            }
            let output = name +
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm getSpawnReport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.getSpawnReport + '</span></div></div>' +
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeMap + '</span></div></div>' +
                  '<div class="input-group"><button class="btn btn-secondary btn-sm exportLayer" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportPolygon + '</span></div></div>' +
                  nameInput + included;
            return output;
        }, {maxWidth: 500, minWidth: 300}).addTo(nestLayer);
      });
    } 
    $('#modalImport').modal('hide');
  });
  $('#importSubmissions').on('click', function(event) {
    subsLayer.clearLayers();
    let pointsData = [];
    if (csvImport != null) {
      pointsData = csvtoarray(csvImport, true);
      csvImport = null;
      $('#csvOpener').val('');
    } else {
      pointsData = csvtoarray($('#importSubmissionsData').val().trim());
      $('#importSubmissionsData').val('');
    }
    let formatCheck = pointsData[0][0];
    if (formatCheck != 'id'){
      pointsData.forEach(function(item) {      
        let marker = L.marker([item[0], item[1]], {
          draggable: true
        }).bindPopup('<span>' + item[2] + '</span>').addTo(subsLayer);
        if ($('#submissionRangeCheck').is(':checked')) {
          marker.rangeID = addPOIRange(marker);
          marker.on('drag', function() {
            subsLayer.removeLayer(marker.rangeID);
            marker.rangeID = addPOIRange(marker);
          })
        };
      });
    } else if (formatCheck == 'id') {
      pointsData.shift();
      pointsData.forEach(function(item) {
        let marker = L.marker([item[4], item[5]], {
          draggable: true
        }).bindPopup('<div style="max-width: 150px;"><p align="center">' + item[2] + '</p><img src="' + item[10] + '" width="150px"></div>').addTo(subsLayer);
        if ($('#submissionRangeCheck').is(':checked')) {
          marker.rangeID = addPOIRange(marker);
          marker.on('drag', function() {
            subsLayer.removeLayer(marker.rangeID);
            marker.rangeID = addPOIRange(marker);
          })
        };
      });
    } else {
      alert('Something went horribly wrong');
    }
  });
  $('#importInstance').on('click', function(event) {
    let name = $("#importInstanceName" ).val();
    let color = '#' + $("#instanceColor input").val();
    getInstance(name,color);
  });
  $('#importCircles').on('click', function(event) {
    let name = subs.importCirclesIns;
    let color = '#' + $("#instanceColor input").val();
    importCircles(name,color);
  });
  $('#getOptimizedRoute').on('click', function(event) {
    let optimizeForGyms = $('#optimizeForGyms').is(':checked');
    let optimizeForPokestops = $('#optimizeForPokestops').is(':checked');
    let optimizeForSpawnpoints = $('#optimizeForSpawnpoints').is(':checked');
    let optimizeForUnknownSpawnpoints = $('#optimizeForUnknownSpawnpoints').is(':checked');
    let optimizeNests = $('#optimizeNests').is(':checked');
    let optimizePolygons = $('#optimizePolygons').is(':checked');
    let optimizeCircles = $('#optimizeCircles').is(':checked');
    generateOptimizedRoute(optimizeForGyms, optimizeForPokestops, optimizeForSpawnpoints, optimizeForUnknownSpawnpoints, optimizeNests, optimizePolygons, optimizeCircles);
   });
  $('#getAdBounds').on('click', function(event) {
    let adBoundsLv = '';
    if ($('#adBounds1').is(':checked')) {
      adBoundsLv = '6';
    } else if ($('#adBounds2_1').is(':checked')) {
      adBoundsLv = '6';
    } else if ($('#adBounds2_2').is(':checked')) {
      adBoundsLv = '8';
    } else if ($('#adBounds3_1').is(':checked')) {
      adBoundsLv = '9';
    } else if ($('#adBounds3_2').is(':checked')) {
      adBoundsLv = '10';
    } else if ($('#adBounds3_3').is(':checked')) {
      adBoundsLv = '11';
    }
    getAdBounds(adBoundsLv);
   });
  $('#modalSpawnReport').on('hidden.bs.modal', function(event) {
    $('#spawnReportTable > tbody').empty();
    $('#spawnReportTableMissed > tbody').empty();
    $('#modalSpawnReport .modal-title').text();
    $("#modalSpawnReport .writeNest").text(subs.writeToDB);
  });
  $('#modalOutput').on('hidden.bs.modal', function(event) {
    $('#outputCircles').val('');
    $('#outputCirclesCount').val('');
    $('#outputAvgPt').val('');
    $(document.getElementById('copyCircleOutput')).text(subs.copyClipboard);
    $('#exportListCount').val(exportList.getLayers().length);
  });
  $('#modalSettings').on('hidden.bs.modal', function(event) {
    let tileset = null;
    let spawnReportLimit = $('#spawnReportLimit').val();
    let optimizationAttempts = $('#optimizationAttempts').val();
    let cellsLevel0 = $('#cellsLevel0').val();
    let cellsLevel0Check = $('#cellsLevel0Check').is(":checked");
    let cellsLevel1Check = $('#cellsLevel1Check').is(":checked");
    let cellsLevel2Check = $('#cellsLevel2Check').is(":checked");
    let s2CountPOICheck = $('#s2CountPOI').is(":checked");
    let generateS2Check = $('#generateWithS2Cells').is(":checked");
    let nestMigrationDate = moment($("#nestMigrationDate").datetimepicker('date')).local().format('X');
    let oldSpawnpointsTimestamp = moment($("#oldSpawnpointsTimestamp").datetimepicker('date')).local().format('X');
    let oldTlChoice = settings.tlChoice;
    let tlChoice = $('#tlChoice').val();
    switch(tlChoice) {
      case 'carto':
        tileset = 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png';
      break;
      case 'own':
        tileset = '<?php echo OWN_TS ?>';
      break;
      case 'osm':
        tileset = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
      break;
      case 'sat':
        tileset = 'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
      break;
      case 'topo':
        tileset = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png'
      break;
      case 'dark':
        tileset = 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}.png'
      break;
    }
    let circleSize;
    let selectCircleRange = $("#modalSettings input[name=selectCircleRange]:checked").val()
    if (selectCircleRange == 'circleIV') {
      circleSize = 70;
    } else if (selectCircleRange == 'circleRaid') {
      circleSize = 'raid';
    } else if (selectCircleRange == 'circle1gb') {
      circleSize = '1gb';
    } else {
      let csTemp = $('#circleSize').val();
      circleSize = parseInt(csTemp);
    } 
    let oldLang = settings.language;
    let language = $('#language').val();
    const newSettings = {
      circleSize: circleSize,
      optimizationAttempts: optimizationAttempts,
      cellsLevel0: cellsLevel0,
      cellsLevel0Check: cellsLevel0Check,
      cellsLevel1: 14,
      cellsLevel1Check: cellsLevel1Check,
      cellsLevel2: 17,
      cellsLevel2Check: cellsLevel2Check,
      s2CountPOI: s2CountPOICheck,
      generateWithS2Cells: generateS2Check,
      selectCircleRange: selectCircleRange,
      tlChoice: tlChoice,
      tlLink: tileset,
      nestMigrationDate: nestMigrationDate,
      oldSpawnpointsTimestamp: oldSpawnpointsTimestamp,
      spawnReportLimit: spawnReportLimit,
      language: language
    };
    Object.keys(newSettings).forEach(function(key) {
      if (settings[key] != newSettings[key]) {
        settings[key] = newSettings[key];
        storeSetting(key);
      }
    });
    if (settings.language != oldLang) {
      getLanguage();
      location.reload();
    }
    if (settings.tlChoice != oldTlChoice) {
      location.reload();
    }
    updateS2Overlay() 
  });
  $('#cancelSettings').on('click', function(event) {
    processSettings(true);
  });
  $("#copyCircleOutput").click(function () {
    document.getElementById('outputCircles').select();
    document.execCommand('copy');
    $(this).text(subs.copied);
  });
  $("#copyPolygonOutput").click(function () {
    document.getElementById(copyOutput).select();
    document.execCommand('copy');
    $(this).text(subs.copied);
  });
});
function initMap() {
  let attrOsm = 'Map data &copy; <a href="https://openstreetmap.org/">OpenStreetMap</a> contributors';
  let attrOverpass = 'POI via <a href="https://www.overpass-api.de/">Overpass API</a>';
  let osm = new L.TileLayer(
  settings.tlLink, {  
    attribution: [attrOsm, attrOverpass].join(', ')
  });
  map = L.map('map', {
    zoomDelta: 0.25,
    zoomSnap: 0.25,
    zoomControl: true,
    worldCopyJump: true,
    wheelPxPerZoomLevel: 30}).addLayer(osm).setView(settings.mapCenter, settings.mapZoom);
  circleLayer = new L.FeatureGroup();
  circleLayer.addTo(map);
  bgLayer = new L.FeatureGroup();
  bgLayer.addTo(map);
  editableLayer = new L.FeatureGroup();
  editableLayer.addTo(map);
  admLayer = new L.FeatureGroup();
  admLayer.addTo(map);
  circleS2Layer = new L.FeatureGroup();
  circleS2Layer.addTo(map);
  gymLayer = new L.LayerGroup();
  gymLayer.addTo(map);
  pokestopLayer = new L.LayerGroup();
  pokestopLayer.addTo(map);
  pokestopRangeLayer = new L.LayerGroup();
  pokestopRangeLayer.addTo(map);
  spawnpointLayer = new L.LayerGroup();
  spawnpointLayer.addTo(map);
  viewCellLayer = new L.LayerGroup();
  viewCellLayer.addTo(map);
  pokestopCellLayer = new L.LayerGroup();
  pokestopCellLayer.addTo(map);
  spawnpointCellLayer = new L.LayerGroup();
  spawnpointCellLayer.addTo(map);
  nestLayer = new L.FeatureGroup();
  nestLayer.addTo(map);
  subsLayer = new L.LayerGroup();
  subsLayer.addTo(map);
  exportList = new L.FeatureGroup();
  exportList.addTo(map);
  instanceLayer = new L.FeatureGroup();
  instanceLayer.addTo(map);
  questLayer = new L.FeatureGroup();
  questLayer.addTo(map);
  routingLayer = new L.FeatureGroup();
  routingLayer.addTo(map);
  bootstrapLayer = new L.featureGroup();
  bootstrapLayer.addTo(map);
  
  // changing predefined tooltips
  L.drawLocal.draw.toolbar.buttons.polygon = subs.drawPolygon;
  L.drawLocal.draw.toolbar.buttons.circle = subs.drawCircle;
  L.drawLocal.edit.toolbar.buttons.edit = subs.editEdit;
  L.drawLocal.edit.toolbar.buttons.editDisabled = subs.editEditDisabled;

  // Buttons left
  searchControl = new L.Control.Search({
    url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}',
    jsonpParam: 'json_callback',
    propertyName: 'display_name',
    propertyLoc: ['lat','lon'],
    marker: false,
    autoCollapse: true,
    autoType: false,
    minLength: 2,
    textErr: subs.searchErr, 
    textCancel: subs.searchCancel,
    textPlaceholder: subs.searchPlaceholder
  }).addTo(map);
  buttonLocate = L.control.locate({
    id: 'getOwnLocation',
    position: 'topleft',
    strings: {
      title: subs.getOwnLocation
    },
    setView: 'once',
    drawCircle: false,
    drawMarker: false,
    icon: 'fas fa-crosshairs'
  }).addTo(map);
  drawControl = new L.Control.Draw({
    draw: {
      polyline: false,
      polygon:   {
        shapeOptions: {
          clickable: false
        }
      },
      circle: {
        shapeOptions: {
          color: '#662d91'
        }
      },
      rectangle: false,
      circlemarker: false,
      marker: false
    },
    edit: {
      featureGroup: editableLayer,
      edit: true,
      remove: false,
      poly: false
    }
  }).addTo(map);

  // barShowPolyOpts
  buttonManualCircle = L.easyButton({
    states: [{
      stateName: 'enableManualCircle',
      icon: 'far fa-circle',
      title: subs.enableManualCircle,
      onClick: function (btn) {
        manualCircle = true;
        btn.state('disableManualCircle');
      }
    }, {
      stateName: 'disableManualCircle',
      icon: 'fas fa-circle',
      title: subs.disableManualCircle,
      onClick: function (btn) {
        manualCircle = false;
        btn.state('enableManualCircle');
      }
    }]
  });
  buttonImportAdBounds = L.easyButton({
    states: [{
      stateName: 'openImportAdBoundsModal',
      icon: 'far fa-map',
      title: subs.importAdBounds,
      onClick: function (control){
        $('#modalAdBounds').modal('show');
      }
    }]
  });
  buttonModalImportPolygon = L.easyButton({
    states: [{
      stateName: 'openImportPolygonModal',
      icon: 'fas fa-draw-polygon',
      title: subs.importPolygon,
      onClick: function (control){
        $('#importPolygonData').val('');
        $('#modalImportPolygon').modal('show');
      }
    }]
  });
  buttonModalImportInstance = L.easyButton({
    states: [{
      stateName: 'openImportInstanceModal',
      icon: 'fas fa-truck-loading',
      title: subs.importInstance,
      onClick: function (control){
        getInstance();
        $('#importCircleData').val('');
        $('#modalImportInstance').modal('show');
      }
    }]
  });
  buttonModalNestOptions = L.easyButton({
    states: [{
      stateName: 'openNestOptionsModal',
      icon: 'fa fa-bug',
      title: subs.nestOptions,
      onClick: function (control){
        $("#modalNests .updateButton").text(subs.writeAllToDB);
        $('#modalNests').modal('show');
      }
    }]
  });
  buttonTrashRoute = L.easyButton({
    states: [{
      stateName: 'clearMapRoute',
      icon: 'fas fa-times-circle',
      title: subs.clearRoute,
      onClick: function (control){
        circleLayer.clearLayers();
        bootstrapLayer.clearLayers();
        instanceLayer.clearLayers();
        instances = [];
        circleInstance = [];
      }
    }]
  });
  barShowPolyOpts = L.easyBar([buttonManualCircle, buttonModalNestOptions, buttonImportAdBounds, buttonModalImportPolygon, buttonModalImportInstance, buttonTrashRoute], { position: 'topleft' }).addTo(map);
  
  // barOutput
  buttonGenerateRoute = L.easyButton({
    id: 'generateRoute',
    states:[{
      stateName: 'generateRoute',
      icon: 'fas fa-cookie',
      title: subs.generateRoute,
      onClick: function (btn) {
        generateRoute();
      }
    }]
  });
  buttonOptimizeRoute = L.easyButton({
    id: 'optimizeRoute',
    states:[{
      stateName: 'optimizeRoute',
      icon: 'fas fa-cookie-bite',
      title: subs.generateOptimizedRoute,
      onClick: function (btn) {
        $('#modalOptimize').modal('show');
      }
    }]
  });
  buttonModalOutput = L.easyButton({
    states: [{
      stateName: 'openOutputModal',
      icon: 'far fa-clipboard',
      title: subs.getOutput,
      onClick: function (control){
        $('#modalOutput').modal('show');
        newMSInstances();
      }
    }]
  });
  barOutput = L.easyBar([buttonGenerateRoute, buttonOptimizeRoute, buttonModalOutput], { position: 'topleft' }).addTo(map);

  // barWayfarer
  buttonModalImportSubmissions = L.easyButton({
    states: [{
      stateName: 'openImportSubmissionsModal',
      icon: 'far fa-dot-circle',
      title: subs.importSubmissions,
      onClick: function (control){
        $('#modalImportSubmissions').modal('show');
      }
    }]
  });
  buttonNewPOI = L.easyButton({
    states: [{
      stateName: 'enableNewPOI',
      icon: 'fas fa-map-marker-alt',
      title: subs.enableNewPOI,
      onClick: function (btn) {
        newPOI = true;
        btn.state('disableNewPOI');
      }
    }, {
      stateName: 'disableNewPOI',
      icon: 'fas fa-map-marker',
      title: subs.disableNewPOI,
      onClick: function (btn) {
        newPOI = false;
        btn.state('enableNewPOI');
      }
    }]
  });
  buttonClearSubs = L.easyButton({
    states: [{
      stateName: 'clearSubs',
      icon: 'fas fa-times-circle',
      title: subs.clearSubs,
      onClick: function (control){
        subsLayer.clearLayers();
      }
    }]
  });
  barWayfarer = L.easyBar([buttonModalImportSubmissions, buttonNewPOI, buttonClearSubs], { position: 'topleft' }).addTo(map);

  // Buttons right
  // Bar mapMode
  buttonMapModePoiViewer = L.easyButton({
    id: 'enableMapModePoiViewer',
    states: [{
      stateName: 'enableMapModePoiViewer',
      icon: 'fas fa-binoculars',
      title: subs.poiViewer,
      onClick: function (btn) {
        settings.mapMode = 'PoiViewer';
        storeSetting('mapMode');
        setMapMode();
      }
    }]
  });
  buttonMapModeRouteGenerator = L.easyButton({
    id: 'enableMapModeRouteGenerator',
    states: [{
      stateName: 'enableMapModeRouteGenerator',
      icon: 'fas fa-shapes',
      title: subs.routeGenerator,
      onClick: function (btn) {
        settings.mapMode = 'RouteGenerator';
        storeSetting('mapMode');
        setMapMode();
      }
    }]
  });
  barMapMode = L.easyBar([buttonMapModeRouteGenerator, buttonMapModePoiViewer], { position: 'topright' }).addTo(map);

  //Bar showPOIs
  buttonShowGyms = L.easyButton({
    id: 'showGyms',
    states: [{
      stateName: 'enableShowGyms',
      icon: 'fas fa-dumbbell',
      title: subs.hideGyms,
      onClick: function (btn) {
        settings.showGyms = false;
        storeSetting('showGyms');
        setShowMode();
        }
    }, {
      stateName: 'disableShowGyms',
      icon: 'fas fa-dumbbell',
      title: subs.showGyms,
      onClick: function (btn) {
        settings.showGyms = true;
        storeSetting('showGyms');
        setShowMode();
      }
    }]
  });
  buttonShowPokestops = L.easyButton({
    id: 'showPokestops',
    states: [{
      stateName: 'enableShowPokestops',
      icon: 'fas fa-map-pin',
      title: subs.hidePokestops,
      onClick: function (btn) {
        settings.showPokestops = false;
        storeSetting('showPokestops');
        setShowMode();
      }
    }, {
      stateName: 'disableShowPokestops',
      icon: 'fas fa-map-pin',
      title: subs.showPokestops,
      onClick: function (btn) {
        settings.showPokestops = true;
        storeSetting('showPokestops');
        setShowMode();
      }
    }]
  });
  buttonShowPokestopsRange = L.easyButton({
    id: 'showPokestopsRange',
    states: [{
      stateName: 'enableShowPokestopsRange',
      icon: 'fas fa-layer-group',
      title: subs.hidePokestopRange,
      onClick: function (btn) {
        settings.showPokestopsRange = false;
        storeSetting('showPokestopsRange');
        setShowMode();
      }
    }, {
      stateName: 'disableShowPokestopsRange',
      icon: 'fas fa-layer-group',
      title: subs.showPokestopRange,
      onClick: function (btn) {
        settings.showPokestopsRange = true;
        storeSetting('showPokestopsRange');
        setShowMode();
      }
    }]
  });
  buttonShowSpawnpoints = L.easyButton({
    id: 'showSpawnpoints',
    states:[{
      stateName: 'enableShowSpawnpoints',
      icon: 'fas fa-paw',
      title: subs.hideSpawnpoints,
      onClick: function (btn) {
        settings.showSpawnpoints = false;
        storeSetting('showSpawnpoints');
        setShowMode();
      }
    }, {
      stateName: 'disableShowSpawnpoints',
      icon: 'fas fa-paw',
      title: subs.showSpawnpoints,
      onClick: function (btn) {
        settings.showSpawnpoints = true;
        storeSetting('showSpawnpoints');
        setShowMode();
      }
    }]
  });
  buttonHideOldSpawnpoints = L.easyButton({
    id: 'hideOldSpawnpoints',
    states:[{
      stateName: 'enableHideOldSpawnpoints',
      icon: 'fas fa-history',
      title: subs.hideOldSpawnpoints,
      onClick: function (btn) {
        settings.hideOldSpawnpoints = false;
        storeSetting('hideOldSpawnpoints');
        setShowMode();
      }
    }, {
      stateName: 'disableHideOldSpawnpoints',
      icon: 'fas fa-history',
      title: subs.showOldSpawnpoints,
      onClick: function (btn) {
        settings.hideOldSpawnpoints = true;
        storeSetting('hideOldSpawnpoints');
        setShowMode();
      }
    }]
  });
  buttonShowUnknownPois = L.easyButton({
    id: 'showUnknownPois',
    states:[{
      stateName: 'enableShowUnknownPois',
      icon: 'fas fa-question-circle',
      title: subs.showAllPOIS,
      onClick: function (btn) {
        settings.showUnknownPois = false;
        storeSetting('showUnknownPois');
        setShowMode();
      }
    }, {
      stateName: 'disableShowUnknownPois',
      icon: 'fas fa-question-circle',
      title: subs.showUnknownPOIS,
      onClick: function (btn) {
        settings.showUnknownPois = true;
        storeSetting('showUnknownPois');
        setShowMode();
      }
    }]
  });
  buttonShowMissingQuests = L.easyButton({
    id: 'showMissingQuests',
    states:[{
      stateName: 'enableShowMissingQuests',
      icon: 'fab fa-searchengin',
      title: subs.hideQuests,
      onClick: function (btn) {
        settings.showMissingQuests = false;
        storeSetting('showMissingQuests');
        questLayer.clearLayers();
        setShowMode();
      }
    }, {
      stateName: 'disableShowMissingQuests',
      icon: 'fab fa-searchengin',
      title: subs.showQuests,
      onClick: function (btn) {
        settings.showMissingQuests = true;
        storeSetting('showMissingQuests');
        $('#modalQuestInstances').modal('show');
        newMSQuests();
      }
    }]
  });
  buttonShowRoute = L.easyButton({
    id: 'showRoute',
    states:[{
      stateName: 'enableShowRoute',
      icon: 'fas fa-route',
      title: subs.hideRoute,
      onClick: function (btn) {
        settings.showRoute = false;
        storeSetting('showRoute');
        setShowMode();
      }
    }, {
      stateName: 'disableShowRoute',
      icon: 'fas fa-route',
      title: subs.showRoute,
      onClick: function (btn) {
        settings.showRoute = true;
        storeSetting('showRoute');
        setShowMode();
      }
    }]
  });
  barShowPOIs = L.easyBar([buttonShowGyms, buttonShowPokestops, buttonShowPokestopsRange, buttonShowSpawnpoints, buttonHideOldSpawnpoints, buttonShowUnknownPois, buttonShowMissingQuests, buttonShowRoute], { position: 'topright' }).addTo(map);

  // Bar rightOpts
  buttonTrash = L.easyButton({
    states: [{
      stateName: 'clearMap',
      icon: 'fas fa-trash',
      title: subs.clearShapes,
      onClick: function (control){
        clearAllLayers();
      }
    }]
  });
  buttonTrash.button.style.backgroundColor = '#B7E9B7';
  buttonModalSettings = L.easyButton({
    position: 'topright',
    states: [{
      stateName: 'openSettingsModal',
      icon: 'fas fa-cog',
      title: subs.openSettings,
      onClick: function (control){
        if (settings.circleSize != null && settings.circleSize != 'raid') {
          $('#circleSize').val(settings.circleSize);
        } else {
          $('#circleSize').val('500');
        }
        if (settings.spawnReportLimit != null) {
          $('#spawnReportLimit').val(settings.spawnReportLimit);
        } else {
          $('#spawnReportLimit').val('0');
        }
        if (settings.optimizationAttempts != null) {
          $('#optimizationAttempts').val(settings.optimizationAttempts);
        } else {
          $('#optimizationAttempts').val('10');
        }
        if (settings.cellsLevel0 != null) {
          $('#cellsLevel0').val(settings.cellsLevel0);
        } else {
          $('#cellsLevel0').val();
        }
        if (settings.cellsLevel0Check != false) {
          $('#cellsLevel0Check').checked = settings.cellsLevel0Check;
        }
        if (settings.cellsLevel1Check != null) {
          $('#cellsLevel1Check').checked = settings.cellsLevel1Check;
        }
        if (settings.cellsLevel2Check != null) {
          $('#cellsLevel2Check').checked = settings.cellsLevel2Check;
        }
        if (settings.generateWithS2Cells != false) {
          $('#generateWithS2Cells').checked = true;
        }
        if (settings.selectCircleRange != null) {
          document.getElementById(settings.selectCircleRange).checked = true;
        }
        if (settings.nestMigrationDate != null) {
          settings.nestMigrationDate = (parseInt(settings.nestMigrationDate) < lastNestChange()) ? lastNestChange() : settings.nestMigrationDate;
          $('#nestMigrationDate').datetimepicker('date', moment.unix(settings.nestMigrationDate).utc().local().format('MM/DD/YYYY HH:mm'));
        }
        if (settings.oldSpawnpointsTimestamp != null) {
          $('#oldSpawnpointsTimestamp').datetimepicker('date', moment.unix(settings.oldSpawnpointsTimestamp).utc().local().format('MM/DD/YYYY HH:mm'));
        }
        if (settings.language != null) {
          $('#language').val(settings.language);
        } else {
          $('#language').val('en');
        }
        if (settings.tlChoice != null) {
          $('#tlChoice').val(settings.tlChoice);
        } else {
          $('#tlChoice').val('osm');
        }
        $('#modalSettings').modal('show');
      }
    }]
  });
  buttonModalSettings.button.style.backgroundColor = '#B7E9B7';
  barRightOpts = L.easyBar([buttonTrash, buttonModalSettings], { position: 'topright' }).addTo(map);

  map.on('draw:drawstart', function(e) {
    manualCircle = false;
    newPOI = false;
    buttonManualCircle.state('enableManualCircle');
    buttonNewPOI.state('enableNewPOI');
  });

  map.on('draw:created', function (e) {
    let layer = e.layer;
    layer.addTo(editableLayer);
  });

  editableLayer.on('layeradd', function(e) {
    let layer = e.layer;
    layer.tags = {};
    layer.tags.name = '';
    layer.tags.included = false;
    let area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
    let readableArea = L.GeometryUtil.readableArea(area, true);
    let name = '';
    let nameInput = '';
    let included = '';
    layer.bindPopup(function (layer) {
      if (layer.tags.name == '') {
        name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + subs.polygon + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
        nameInput = '<hr><div class="input-group mb-3">' +
                              '<div class="input-group-prepend">' +
                                '<span class="input-group-text">' + subs.name + '</span>' +
                              '</div>' +
                              '<input id="polygonName" name="polygonName" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="text" class="form-control" aria-label="Polygon name">' +
                            '</div>';
      } else {
        name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + layer.tags.name + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
        nameInput = '<hr>';        
      }
      if (layer.tags.included == true) {
        included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm removeFromExport" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeFromExport + '</span></div></div>';
      } else {
        included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm addToExport" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.addToExport + '</span></div></div>';
      }
      let merge = '';
      if (layer.tags.merged != true) {
            merge = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm mergePolygons" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.mergePolygons + '</span></div></div>';
      }
      let output;
      if (layer.getRadius != undefined) {
//        Kreis-Popup
        let latOut = layer.getLatLng().lat.toFixed(5);
        let lonOut = layer.getLatLng().lng.toFixed(5);
        let radiusOut = layer.getRadius().toFixed(2);
        output = '<div align="center"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="editableLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.delete + '</button></div>' + '<p style="padding-left: 70px;">Radius: ' + radiusOut + 'm<br>' + subs.coords + ':<br>' + latOut + ', ' + lonOut + '</p></div>';
      } else {
        output = name +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm getSpawnReport" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.getSpawnReport + '</span></div></div>' +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeMap + '</span></div></div>' +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm exportLayer" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportPolygon + '</span></div></div>' +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm exportPoints" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportVP + '</span></div></div>' +
                   '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm countPoints" data-layer-container="editableLayer" data-layer-id=' +
                   layer._leaflet_id +
                   ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.countVP + '</span></div></div>' +
                   nameInput + included + merge;
      }
      return output;
    }, {maxWidth: 500, minWidth: 300});
  });
  circleLayer.on('layerremove', function(e) {
    let layer = e.layer;
    layer.s2cells.forEach(function(item) {
      circleS2Layer.removeLayer(parseInt(item));
    });
    let id = circleInstance.id;
    let name = circleInstance.name;
    circleInstance = removeArrayElement(circleInstance, layer._leaflet_id)
    circleInstance.id = id;
    circleInstance.name = name;
    instances[id] = removeArrayElement(instances[id], layer._leaflet_id);
    instances[id].id = id;
    instances[id].name = name;
  });
  circleLayer.on('layeradd', function(e) {
    drawCircleS2Cells(e.layer);
    circleLayer.removeFrom(map).addTo(map);
    e.layer.on('drag', function() {
      drawCircleS2Cells(e.layer);
    })
  });
  instanceLayer.on('layerremove', function(e) {
    let layer = e.layer;
    if (typeof layer.s2cells !== 'undefined') {
      layer.s2cells.forEach(function(item) {
        circleS2Layer.removeLayer(item);
      });
    }
    let id = layer.options.instanceID;
    let name = instances[id].name;
    instances[id] = removeArrayElement(instances[id], layer._leaflet_id);
    instances[id].id = id;
    instances[id].name = name;
    if (routingLayer.getLayers().length > 1) {
      instances[id].forEach(function(item) {  
        let x = instanceLayer.getLayer(parseInt(item)).lineID;
        routingLayer.removeLayer(parseInt(x))
      });
      routingLayer.removeLayer(parseInt(layer.lineID));
    }
    if (instances[id].length > 0) {
      drawRoute(instances[id]);
    }
  });
  instanceLayer.on('layeradd', function(e) {
    drawCircleS2Cells(e.layer);
    instanceLayer.removeFrom(map).addTo(map);
    e.layer.on('drag', function() {
      drawCircleS2Cells(e.layer)
      instances[e.layer.options.instanceID].forEach(function(item) {
        let x = instanceLayer.getLayer(parseInt(item)).lineID;
        routingLayer.removeLayer(parseInt(x))
      });
      instanceLayer.bringToFront();
      drawRoute(instances[e.layer.options.instanceID]);
    })
  });
  questLayer.on('layerremove', function() {
    settings.showMissingQuests = false;
    storeSetting('showMissingQuests');
  });
  map.on('moveend', function() {
    settings.mapCenter = map.getCenter();
    storeSetting('mapCenter');
    settings.mapZoom = map.getZoom();
    storeSetting('mapZoom');
    loadData();
  });
  map.on('click', function(e) {
    let lat = Math.abs(e.latlng.lat);
    let latOut = lat.toFixed(5);
    let lonOut = Math.abs(e.latlng.lng).toFixed(5);
    let radius;
    if (manualCircle === true) {
      if (settings.circleSize == 'raid' ) {
        if (lat <= 39) {
          radius = 715;
        } else if (lat >= 69) {
          radius = 330;
        } else {
          radius = -13 * lat + 1225;
        }
      } else if (settings.circleSize == '1gb') {
        if (lat <= 39) {
          radius = 715;
        } else if (lat >= 69) {
          radius = 330;
        } else {
          radius = -13 * lat + 1225;
        }
        radius = radius/2;
      } else {
        radius = settings.circleSize;
      }
      let radiusOut = radius.toFixed(2);
      let newCircle = new L.circle(e.latlng, {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        draggable: true,
        radius: radius
      }).bindPopup(function (layer) {
        return '<button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="circleLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.delete + '</button></div><div class="input-group mb-3"><button class="btn btn-secondary btn-sm sortInstance" data-layer-container="circleLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.newRoute + '</button></div>' + '<p>Radius: ' + radiusOut + 'm<br>' + subs.coords + ':<br>' + latOut + ', ' + lonOut + '</p>';
      }).addTo(circleLayer);
      if (circleInstance == '') {
        circleInstance.push(newCircle._leaflet_id);
        if (instances.length != 'undefined') {
          circleInstance.id = instances.length;
        } else {
          circleInstance.id = 0;
        }
        circleInstance.name = subs.drawnCircles;
        instances.push(circleInstance);
      } else {
        instances[circleInstance.id].push(newCircle._leaflet_id);
      }
    }
  });
  subsLayer.on('layerremove', function(e) {
    let layer = e.layer;
    layer.forEach(function(item) {
      subsLayer.removeLayer(parseInt(item));
    });
  });

  map.on('click', function(e) {
    if (newPOI === true) {
      let marker = L.marker(e.latlng, {
        draggable: true
      }).bindPopup(function (layer) {
        return '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="subsLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.delete + '</button><button class="btn btn-secondary btn-sm exportPOIs" data-layer-container="subsLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.exportPOIs + '</button></div>'}).addTo(subsLayer);
      marker.rangeID = addPOIRange(marker);
      marker.on('drag', function() {
        subsLayer.removeLayer(marker.rangeID);
        marker.rangeID = addPOIRange(marker);
      })
    };
  });
}
function addPOIRange (layer) {
  let range = L.circle(layer.getLatLng(), {
    color: 'black',
    fillColor: 'red',
    radius: 20,
    weight: 1,
    opacity: 1,
    fillOpacity: 0.3
  }).addTo(subsLayer);
  return range._leaflet_id;
}
function drawCircleS2Cells(layer) {
  if (typeof layer.s2cells !== 'undefined') {
    layer.s2cells.forEach(function(item) {
      circleS2Layer.removeLayer(parseInt(item));
    });
  }
  let center = layer.getLatLng()
  let radius = layer.getRadius();
  layer.s2cells = [];
  function addPoly(cell) {
    const vertices = cell.getCornerLatLngs()
    const poly = L.polygon(vertices,{
      color: layer.options.color,
      opacity: 0.8,
      weight: 2,
      fillOpacity: 0.2
    });
    let line = turf.polygonToLine(poly.toGeoJSON());
    let point = turf.point([center.lng, center.lat]);
    let distance = turf.pointToLineDistance(point, line, { units: 'meters' });
    if (distance <= radius) {
      circleS2Layer.addLayer(poly);
      layer.s2cells.push(poly._leaflet_id);
    }
  }
  if (radius < 1000 && radius > 200) {
    let count = 10;
    let cell = S2.S2Cell.FromLatLng(layer.getLatLng(), 15)
    let steps = 1
    let direction = 0
    do {
        for (let i = 0; i < 2; i++) {
            for (let i = 0; i < steps; i++) {
                addPoly(cell)
                cell = cell.getNeighbors()[direction % 4]
            }
            direction++
        }
        steps++
    } while (steps < count)
  }
}
function setShowMode() {
  if (settings.showGyms !== false) {
    buttonShowGyms.state('enableShowGyms');
    buttonShowGyms.button.style.backgroundColor = '#B7E9B7';
  } else {
    gymLayer.clearLayers();
    buttonShowGyms.state('disableShowGyms');
    buttonShowGyms.button.style.backgroundColor = '#E9B7B7';
  }
  if (settings.showPokestops !== false) {
    buttonShowPokestops.state('enableShowPokestops');
    buttonShowPokestops.button.style.backgroundColor = '#B7E9B7';
  } else {
    pokestopLayer.clearLayers();
    buttonShowPokestops.state('disableShowPokestops');
    buttonShowPokestops.button.style.backgroundColor = '#E9B7B7';
  }
  if (settings.showPokestopsRange !== false) {
    buttonShowPokestopsRange.state('enableShowPokestopsRange');
    buttonShowPokestopsRange.button.style.backgroundColor = '#B7E9B7';
  } else {
    pokestopRangeLayer.clearLayers();
    buttonShowPokestopsRange.state('disableShowPokestopsRange');
    buttonShowPokestopsRange.button.style.backgroundColor = '#E9B7B7';
  }
  if (settings.showSpawnpoints !== false) {
    buttonShowSpawnpoints.state('enableShowSpawnpoints');
    buttonShowSpawnpoints.button.style.backgroundColor = '#B7E9B7';
  } else {
    spawnpointLayer.clearLayers();
    buttonShowSpawnpoints.state('disableShowSpawnpoints');
    buttonShowSpawnpoints.button.style.backgroundColor = '#E9B7B7';
  }
  if (settings.hideOldSpawnpoints !== false) {
    buttonHideOldSpawnpoints.state('enableHideOldSpawnpoints');
    buttonHideOldSpawnpoints.button.style.backgroundColor = '#B7E9B7';
  } else {
    spawnpointLayer.clearLayers();
    buttonHideOldSpawnpoints.state('disableHideOldSpawnpoints');
    buttonHideOldSpawnpoints.button.style.backgroundColor = '#E9B7B7';
  }
  if (settings.showUnknownPois !== false) {
    buttonShowUnknownPois.state('enableShowUnknownPois');
    buttonShowUnknownPois.button.style.backgroundColor = '#B7E9B7';
  } else {
    buttonShowUnknownPois.state('disableShowUnknownPois');
    buttonShowUnknownPois.button.style.backgroundColor = '#E9B7B7';
  }
  if (settings.showMissingQuests !== false) {
    buttonShowMissingQuests.state('enableShowMissingQuests');
    buttonShowMissingQuests.button.style.backgroundColor = '#B7E9B7';
  } else {
    buttonShowMissingQuests.state('disableShowMissingQuests');
    buttonShowMissingQuests.button.style.backgroundColor = '#E9B7B7';
  }
  if (settings.showRoute !== false) {
    if (instances != '') {
      instances.forEach(function(item){
        drawRoute(item)
      });  
    }  
    buttonShowRoute.state('enableShowRoute');
    buttonShowRoute.button.style.backgroundColor = '#B7E9B7';
  } else {
    routingLayer.clearLayers();
    buttonShowRoute.state('disableShowRoute');
    buttonShowRoute.button.style.backgroundColor = '#E9B7B7';
  }
  loadData();
}
function setMapMode(){
  switch (settings.mapMode) {
    case 'RouteGenerator':
      buttonMapModePoiViewer.button.style.backgroundColor = '#E9B7B7';
      buttonMapModeRouteGenerator.button.style.backgroundColor = '#B7E9B7';
      $('.leaflet-draw').show();
      barShowPolyOpts.enable();
      barOutput.enable();
      barWayfarer.disable();
      newPOI = false;
      break;
    case 'PoiViewer':
      buttonMapModePoiViewer.button.style.backgroundColor = '#B7E9B7';
      buttonMapModeRouteGenerator.button.style.backgroundColor = '#E9B7B7';
      $('.leaflet-draw').hide();
      barShowPolyOpts.disable();
      barOutput.disable();
      barWayfarer.enable();
      manualCircle = false;
      break;
  }
}
function getInstance(instanceName = null, color = '#1090fa') {
  if (instanceName === null) {
    //get names of all instances
    const data = {
      'get_instance_names': true,
    };
    const json = JSON.stringify(data);
    $.ajax({
      url: this.href,
      type: 'POST',
      dataType: 'json',
      data: {'data': json},
      success: function (result) {
        let select = $('#importInstanceName');
        select.empty();
        result.forEach(function(item) {
          select.append($("<option>").attr('value',item.name).text(item.name + " (" + item.type + ")"));
        });
      }
    });
  } else {
    //get single instance
    const data = {
      'get_instance_data': true,
      'instance_name': instanceName
    };
    const json = JSON.stringify(data);
    let polygonOptions = {
      clickable: false,
      color: color,
      fill: true,
      fillColor: null,
      fillOpacity: 0.2,
      opacity: 0.5,
      stroke: true,
      weight: 4
    };
    let radius = 0;
    if ($('#instanceRadiusCheck').is(":checked")) {
      radius = $('#ownRadius').val();
    } 
    $.ajax({
      url: this.href,
      type: 'POST',
      dataType: 'json',
      data: {'data': json},
      success: function (result) {
        points = result.data.area;
        let distanceAll = 0;
        let routeLength = 0;
        if (result.type == 'circle_pokemon' || result.type == 'circle_smart_pokemon' || result.type == 'circle_raid') {
          for (i=0;i<points.length-1;i++) {
            let pointA = L.point(points[i].lat, points[i].lon);
            let pointB = L.point(points[i+1].lat, points[i+1].lon);
            let distance = pointA.distanceTo(pointB)*100;
            distanceAll += distance;
          }
          let avgDistance = (distanceAll/points.length*1000).toFixed(2);
          console.log('average distance: ' + avgDistance + 'm')
          routeLength = distanceAll.toFixed(3);
        }
        if (points.length > 0 ) {
          if (result.type == 'circle_pokemon' || result.type == 'circle_smart_pokemon') {
            if (!($('#instanceRadiusCheck').is(":checked"))) {
              radius = 70;
            }
            let instance = [];
            instance.name = instanceName;
            instance.id = instances.length;
            instance.routeLength = routeLength;
            points.forEach(function(item) {
              if ($('#instanceMode').is(':checked')) {
                newCircle = L.circle(item, {
                  route_counter: points.length,
                  route_length: routeLength,
                  color: '#b410fa',
                  fillOpacity: 0.4,
                  draggable: false,
                  radius: radius
                }).addTo(bgLayer);
              } else {
                newCircle = L.circle(item, {
                  route_counter: points.length,
                  route_length: routeLength,
                  color: color,
                  fillOpacity: 0.2,
                  draggable: true,
                  radius: radius,
                  instanceID: instance.id
                }).bindPopup(function (layer) {
                  return getCircleHtml(instanceName, layer, subs);
                }).addTo(instanceLayer);
                instance.push(newCircle._leaflet_id);
              }              
            });
            instances.push(instance);
            drawRoute(instance);
          } else if (result.type == 'circle_raid') {
            let instance = [];
            instance.name = instanceName;
            instance.id = instances.length;
            points.forEach(function(item) {
              let lat = Math.abs(item.lat);
              if (!($('#instanceRadiusCheck').is(":checked"))) {
                if (lat <= 39) {
                  radius = 715;
                } else if (lat >= 69) {
                  radius = 330;
                } else {
                  radius = -13 * lat + 1225;
                }
              }
              if ($('#instanceMode').is(':checked')) {
                newCircle = L.circle(item, {
                  route_counter: points.length,
                  route_length: routeLength,
                  color: '#b410fa',
                  fillOpacity: 0.4,
                  draggable: false,
                  radius: radius
                }).addTo(bgLayer);
              } else {
                newCircle = L.circle(item, {
                  route_counter: points.length,
                  route_length: routeLength,
                  color: color,
                  fillOpacity: 0.2,
                  draggable: true,
                  radius: radius,
                  instanceID: instance.id
                }).bindPopup(function (layer) {
                  return getCircleHtml(instanceName, layer, subs);
                }).addTo(instanceLayer);
                instance.push(newCircle._leaflet_id);
              }
            });
            instances.push(instance);
            drawRoute(instance);
          } else if (result.type == 'auto_quest' || result.type == 'pokemon_iv') {
            points.forEach(function(coords) {
              newPolygon = L.polygon(coords, polygonOptions).addTo(editableLayer);
              let area = L.GeometryUtil.geodesicArea(newPolygon.getLatLngs()[0]);
              let readableArea = L.GeometryUtil.readableArea(area, true);
            });
          }
        }
      }
    });
  }
}
function drawRoute(instance) {
  let distanceAll = 0;
  let color;
  if (settings.showRoute != false && instance != '' && instance.name != subs.drawnCircles) {
    for (i=0;i<instance.length-1;i++) {
      let pointA = instanceLayer.getLayer(instance[i]);
      let pointB = instanceLayer.getLayer(instance[i+1]);
      let distance = parseInt(pointA.getLatLng().distanceTo(pointB.getLatLng()).toFixed(2));
      if (distance <= pointA.getRadius()*4) {
        color = 'green';
      } else if (distance >= pointA.getRadius()*6) {
        color = 'red';
      } else {
        color = 'darkorange';
      }
      let line = L.polyline([pointB.getLatLng(),pointA.getLatLng()], {color: color, weight: 2, opacity: 0.8});
      line.addTo(routingLayer);
      pointA.lineID = line._leaflet_id;
      pointA.distanceToNext = distance;
      distanceAll += distance;
    }
    let pointA = instanceLayer.getLayer(instance[instance.length-1]);
    let pointB = instanceLayer.getLayer(instance[0]);
    let distance = parseInt(pointA.getLatLng().distanceTo(pointB.getLatLng()).toFixed(2));
    if (distance <= pointA.getRadius()*4) {
      color = 'green';
    } else if (distance >= pointA.getRadius()*6) {
      color = 'red';
    } else {
      color = 'darkorange';
    }
    let line = L.polyline([pointB.getLatLng(),pointA.getLatLng()], {color: color, weight: 2, opacity: 0.8});
    line.addTo(routingLayer);
    pointA.lineID = line._leaflet_id;
    pointA.distanceToNext = distance;
    distanceAll += distance;
    instance.distance = distanceAll;
    instance.avgDistance = distanceAll/instance.length.toFixed(2);
  }
}
function importCircles(instanceName = null, color = '#1090fa') {
  let circleData = [];
  let radius = 0;
  let importReady = false;

  if ($('#importCircleData').val() != '') {
    circleData = csvtoarray($('#importCircleData').val());
    importReady = true;
  }  
  if ($('#instanceRadiusCheck').is(":checked")) {
    radius = $('#ownRadius').val();
  }

  if (circleData.length > 0 && importReady != false) {
    if (settings.circleSize != 'raid' && settings.circleSize != '1gb') {
      if (!($('#instanceRadiusCheck').is(":checked"))) {
        radius = settings.circleSize;
      }
      let distanceAll = 0;
      for (i=0;i<circleData.length-1;i++) {
        let pointA = L.point(circleData[i][0], circleData[i][1]);
        let pointB = L.point(circleData[i+1][0], circleData[i+1][1]);
        let distance = pointA.distanceTo(pointB)*100;
        distanceAll += distance;
      }
      let routeLength = distanceAll.toFixed(3);
      let avgDistance = (distanceAll/circleData.length*1000).toFixed(2);
      console.log('average distance: ' + avgDistance + 'm')
      let instance = [];
      instance.name = instanceName;
      instance.id = instances.length;
      instance.routeLength = routeLength;
      circleData.forEach(function(item) {
        if ($('#instanceMode').is(':checked')) {
          newCircle = L.circle(item, {
            route_counter: circleData.length,
            route_length: routeLength,
            color: '#b410fa',
            fillOpacity: 0.4,
            draggable: false,
            radius: radius
          }).addTo(bgLayer);
        } else {
          newCircle = L.circle(item, {
            route_counter: circleData.length,
            route_length: routeLength,
            color: color,
            fillOpacity: 0.2,
            draggable: true,
            radius: radius,
            instanceID: instance.id
          }).bindPopup(function (layer) {
            return getCircleHtml(instanceName, layer, subs);
          }).addTo(instanceLayer);
          instance.push(newCircle._leaflet_id);
        }         
      });
      instances.push(instance);
      drawRoute(instance);
    } else if (settings.circleSize == 'raid' || settings.circleSize == '1gb') {
      let instance = [];
      instance.name = instanceName;
      instance.id = instances.length;
      circleData.forEach(function(item) {
        let lat = Math.abs(item.lat);
        if (!($('#instanceRadiusCheck').is(":checked"))) {
          if (lat <= 39) {
            radius = 715;
          } else if (lat >= 69) {
            radius = 330;
          } else {
            radius = -13 * lat + 1225;
          }
          if (settings.circleSize == '1gb') {
            radius = radius/2;
          }
        }
        if ($('#instanceMode').is(':checked')) {
          newCircle = L.circle(item, {
            route_counter: circleData.length,
            route_length: routeLength,
            color: '#b410fa',
            fillOpacity: 0.4,
            draggable: false,
            radius: radius
          }).addTo(bgLayer);
        } else {
          newCircle = L.circle(item, {
            route_counter: circleData.length,
            route_length: routeLength,
            color: color,
            fillOpacity: 0.2,
            draggable: true,
            radius: radius,
            instanceID: instance.id
          }).bindPopup(function (layer) {
            return '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="instanceLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.delete + '</button></div><div class="input-group mb-3"><button class="btn btn-secondary btn-sm sortInstance" data-layer-container="instanceLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.newRoute + '</button></div>';
          }).addTo(instanceLayer);
          instance.push(newCircle._leaflet_id);
        }
      });
      instances.push(instance);
      drawRoute(instance);
    }
  }
}

function getCircleHtml(instance_name, layer, subs) {
  const htmlString = '<div class="input-group mb-3"><label class="form-check-label"><b>' + instance_name + '</b><br>' + subs.countCircles + ' ' + layer.options.route_counter + '<br>' + subs.instanceLength + ' ' + layer.options.route_length + ' km<br>' + subs.circleID + ' ' + layer._leaflet_id + '<br>' + subs.circleRadius + ' ' + layer.options.radius + 'm<br>' + subs.coords + ' (lat,lon):<br>' + layer._latlng.lat + ', ' + layer._latlng.lng + '</label></div><div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="instanceLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.delete + '</button></div><div class="input-group mb-3"><button class="btn btn-secondary btn-sm sortInstance" data-layer-container="instanceLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.newRoute + '</button></div>';
  return htmlString
}

function generateOptimizedRoute(optimizeForGyms, optimizeForPokestops, optimizeForSpawnpoints, optimizeForUnknownSpawnpoints, optimizeNests, optimizePolygons, optimizeCircles) {
  $("#modalLoading").modal('show');
  let newCircle,
    currentLatLng,
    circleRadius,
    point;
  let pointsOut = [];
  instanceLayer.clearLayers();
  circleInstance = [];
  instances = [];
  let lat = Math.abs(map.getCenter().lat);
  if (settings.circleSize == 'raid' || settings.circleSize == '1gb') {
    if (lat <= 39) {
      circleRadius = 715;
    } else if (lat >= 69) {
      circleRadius = 330;
    } else {
      circleRadius = -13 * lat + 1225;
    }
    if (settings.circleSize == '1gb') {
      circleRadius = circleRadius/2;
    }
  } else {
    circleRadius = settings.circleSize;
  }
  let data = {
    'get_optimization': true,
    'circle_size': circleRadius,
    'optimization_attempts': settings.optimizationAttempts,
    'do_tsp': false,
    'points': []
  };
  let routeLayers = function(layer) {
    let points = [];
    let poly = layer.toGeoJSON();
    let line = turf.polygonToLine(poly);
    if (optimizeForGyms == true) {
      gyms.forEach(function(item) {
        point = turf.point([item.lng, item.lat]);
        if (turf.inside(point, poly)) {
          points.push(item)
        }
      });
    }
    if (optimizeForPokestops == true) {
      pokestops.forEach(function(item) {
        point = turf.point([item.lng, item.lat]);
        if (turf.inside(point, poly)) {
          points.push(item)
        }
      });
    }
    if (optimizeForSpawnpoints == true) {
      spawnpoints.forEach(function(item) {
        point = turf.point([item.lng, item.lat]);
        if (turf.inside(point, poly)) {
          points.push(item)
        }
      });
    }
    if (optimizeForUnknownSpawnpoints == true) {
      spawnpoints_u.forEach(function(item) {
        point = turf.point([item.lng, item.lat]);
        if (turf.inside(point, poly)) {
          points.push(item)
        }
      });
    }
    if(points.length > 0) {
      getRoute(points);
    }
  }
  let routeCircles = function(layer) {
    let points = []
    let radius = layer.getRadius();
    let bounds = layer.getBounds();
    let center = bounds.getCenter();
    if (optimizeForGyms == true) {
      gyms.forEach(function(item) {
        let workingLatLng = L.latLng(item.lat, item.lng);
        let distance = workingLatLng.distanceTo(center)
        if (distance <= radius) {
          points.push(item);
        }
      });
    }
    if (optimizeForPokestops == true) {
      pokestops.forEach(function(item) {
        let workingLatLng = L.latLng(item.lat, item.lng);
        let distance = workingLatLng.distanceTo(center)
        if (distance <= radius) {
          points.push(item);
        }
      });
    }
    if (optimizeForSpawnpoints == true) {
      spawnpoints.forEach(function(item) {
        let workingLatLng = L.latLng(item.lat, item.lng);
        let distance = workingLatLng.distanceTo(center)
        if (distance <= radius) {
          points.push(item);
        }
      });
    }
    if (optimizeForUnknownSpawnpoints == true) {
      spawnpoints_u.forEach(function(item) {
        let workingLatLng = L.latLng(item.lat, item.lng);
        let distance = workingLatLng.distanceTo(center)
        if (distance <= radius) {
          points.push(item);
        }
      });
    }
    if(points.length > 0) {
      return points;
    }
  }
  let getRoute = function(points) {
    data.points = _.uniq(points);
    const json = JSON.stringify(data);
    $.ajax({
      beforeSend: function() {
      },
      url: this.href,
      type: 'POST',
      dataType: 'json',
      data: {'data': json},
      success: function (result) {
          result.bestAttempt.forEach(function(point) {
           newCircle = L.circle([point.lat, point.lng], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            draggable: true,
            radius: circleRadius
          }).bindPopup(function (layer) {
            return '<button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="circleLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.delete + '</button></div><div class="input-group mb-3"><button class="btn btn-secondary btn-sm sortInstance" data-layer-container="circleLayer" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.newRoute + '</button></div>';
          }).addTo(circleLayer);
          if (circleInstance == '') {
            circleInstance.push(newCircle._leaflet_id);
            if (instances.length != 'undefined') {
              circleInstance.id = instances.length;
            } else {
              circleInstance.id = 0;
            }
            circleInstance.name = subs.drawnCircles;
            instances.push(circleInstance);
          } else {
            instances[circleInstance.id].push(newCircle._leaflet_id);
          }
        });
      },
      complete: function() { }
    });
  }
  if (optimizePolygons == true) {
    editableLayer.eachLayer(function (layer) {
       routeLayers(layer);
    });
  }
  if (optimizeNests == true) {
    nestLayer.eachLayer(function (layer) {
       routeLayers(layer);
    });
  }
  if (optimizeCircles == true) {
    circleLayer.eachLayer(function (layer) {
      pointsOut = pointsOut.concat(routeCircles(layer));
      circleLayer.removeLayer(layer);
    });
    getRoute(pointsOut);
  }
  $("#modalLoading").modal('hide');
}
function generateRoute() {
  let targetLayer;
  let targetLayerName;
  if (settings.generateWithS2Cells != false) {
    targetLayer = circleLayer;
    targetLayerName = 'circleLayer';
  } else {
    targetLayer = bootstrapLayer;
    targetLayerName = 'bootstrapLayer';
  }
  circleLayer.clearLayers();
  instanceLayer.clearLayers();
  bootstrapLayer.clearLayers();
  circleInstance = [];
  instances = [];
  let circleRadius;
  let lat = Math.abs(map.getCenter().lat);
  if (settings.circleSize == 'raid' || settings.circleSize == '1gb') {
    if (lat <= 39) {
      circleRadius = 715;
    } else if (lat >= 69) {
      circleRadius = 330;
    } else {
      circleRadius = -13 * lat + 1225;
    }
    if (settings.circleSize == '1gb') {
      circleRadius = circleRadius/2;
    }
  } else {
    circleRadius = settings.circleSize;
  }
  let xMod = Math.sqrt(0.75);
  let yMod = Math.sqrt(0.568);
  let route = function(layer) {
    let poly = layer.toGeoJSON();
    let line = turf.polygonToLine(poly);
    let newCircle;
    let currentLatLng = layer.getBounds().getNorthEast();
    let startLatLng = L.GeometryUtil.destination(currentLatLng, 90, circleRadius*1.5);
    let endLatLng = L.GeometryUtil.destination(L.GeometryUtil.destination(layer.getBounds().getSouthWest(), 270, circleRadius*1.5), 180, circleRadius);
    let row = 0;
    let heading = 270;
    let i = 0;
    while (currentLatLng.lat > endLatLng.lat) {
      do {
        let point = turf.point([currentLatLng.lng, currentLatLng.lat]);
        let distance = turf.pointToLineDistance(point, line, { units: 'meters' });
        if (distance <= circleRadius || distance == 0 || turf.inside(point, poly)) {
          newCircle = L.circle(currentLatLng, {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            draggable: true,
            radius: circleRadius
          }).bindPopup(function (layer) {
            return '<button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="' + targetLayerName + '" data-layer-id=' + layer._leaflet_id + ' type="button">' + subs.delete + '</button><div class="input-group mb-3"><button class="btn btn-secondary btn-sm sortInstance" data-layer-container="' + targetLayerName + '" data-layer-id="' + layer._leaflet_id + '" type="button">' + subs.newRoute + '</button></div></div>';
          }).addTo(targetLayer);
          if (circleInstance == '') {
            circleInstance.push(newCircle._leaflet_id);
            if (instances.length != 'undefined') {
              circleInstance.id = instances.length;
            } else {
              circleInstance.id = 0;
            }
            circleInstance.name = 'bootstrap';
            instances.push(circleInstance);
          } else {
            instances[circleInstance.id].push(newCircle._leaflet_id);
          }
        }
        currentLatLng = L.GeometryUtil.destination(currentLatLng, heading, (xMod*circleRadius*2));
        i++;
      } while ((heading == 270 && currentLatLng.lng > endLatLng.lng) || (heading == 90 && currentLatLng.lng < startLatLng.lng));
      currentLatLng = L.GeometryUtil.destination(currentLatLng, 180, (yMod*circleRadius*2));
      rem = row%2;
      if (rem == 1) {
        heading = 270;
      } else {
        heading = 90;
      }
      currentLatLng = L.GeometryUtil.destination(currentLatLng, heading, (xMod*circleRadius)*3);
      row++;
    }
  }
  editableLayer.eachLayer(function (layer) {
    route(layer);
  });
  nestLayer.eachLayer(function (layer) {
    route(layer);
  });
}
function removeArrayElement(arr, value) {
  return arr.filter(function(ele){
    return ele != value;
  });
}
function writeNests(nestId, nestPokemon, avgSpawns, nowUt, pkmCount, nestName, lat, lon, path) {
  const data = {
    'set_nest_data': true,
    'nest_id': nestId,
    'nest_pokemon': nestPokemon,
    'avg_spawns': avgSpawns,
    'updated': nowUt,
    'pokemon_count': pkmCount,
    'name': nestName,
    'lat': lat,
    'lon': lon,
    'path': path
  }
  const json = JSON.stringify(data);
  $.ajax({
    url: this.href,
    type: 'POST',
//    async: false,
    dataType: 'json',
    data: {'data': json},
    success: function (result) {
      if (result == 1) {
        $("#modalSpawnReport .writeNest").text(subs.writeSuccess);
      }
    },
    error: function () {
      console.log('Something went horribly wrong')
    }
  });
}
function importNests() {
  nestLayer.clearLayers();
  const data = {
    'get_nest_data': true,
  }
  const json = JSON.stringify(data);
  $.ajax({
    url: this.href,
    type: 'POST',
    async: false,
    dataType: 'json',
    data: {'data': json},
    success: function (result) {
        result.nests.forEach(function(feature) {
          let gjFeature = turf.polygon(JSON.parse(feature.polygon_path));
          let coordinates = (turf.flip(gjFeature)).geometry.coordinates;
          let polygon = L.polygon(coordinates, {
            clickable: false,
            color: "#ff8833",
            fill: true,
            fillColor: null,
            fillOpacity: 0.2,
            opacity: 0.5,
            stroke: true,
            weight: 4
          });
          polygon.tags = {};
          polygon.tags.osmid = feature.nest_id;
          if (feature.name != undefined && feature.name != 'Unknown Parkname') {
            polygon.tags.name = feature.name;
          } else {
            polygon.tags.name = '';
          }
          polygon.tags.path = feature.polygon_path;
          polygon.tags.centerLat = JSON.parse(feature.lat);
          polygon.tags.centerLon = JSON.parse(feature.lon);
          polygon.tags.included = false;
          let area = L.GeometryUtil.geodesicArea(polygon.getLatLngs()[0]);
          let readableArea = L.GeometryUtil.readableArea(area, true);
          polygon.addTo(nestLayer);
          let name = '';
          let nameInput = '';
          let included = '';
          polygon.bindPopup(function (layer) {
            if (typeof layer.tags.name !== 'undefined') {
              name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + subs.nest + ': ' + layer.tags.name + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
              nameInput = '<hr>';
            } else {
              name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + subs.polygon + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
              nameInput = '<hr><div class="input-group mb-3">' +
                              '<div class="input-group-prepend">' +
                                '<span class="input-group-text">' + subs.name + '</span>' +
                              '</div>' +
                              '<input id="polygonName" name="polygonName" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="text" class="form-control" aria-label="Polygon name">' +
                            '</div>';
            }
            if (layer.tags.included == true) {
              included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm removeFromExport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeFromExport + '</span></div></div>';
            } else {
              included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm addToExport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.addToExport + '</span></div></div>';
            }
            let output = name +
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm getSpawnReport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.getSpawnReport + '</span></div></div>' +
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeMap + '</span></div></div>' +
                  '<div class="input-group"><button class="btn btn-secondary btn-sm exportLayer" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportPolygon + '</span></div></div>' +
                  nameInput + included;
            return output;
          }, {maxWidth: 500, minWidth: 300});
        });
      
    },
    error: function () {
      console.log('Something went horribly wrong')
    }
  });
}
function prepareData(layerBounds) {
  spawnpoints = [];
  pokestops = [];
  gyms = [];
  let bounds;
  if (layerBounds._northEast != undefined) {
    bounds = layerBounds;
  } else if (circleLayer.getLayers().length > 1) {
    bounds = circleLayer.getBounds();
  } else if (instanceLayer.getLayers().length > 1) {
    bounds = instanceLayer.getBounds();
  } else {
    bounds = map.getBounds();
  }
  const data = {
    'get_data': true,
    'min_lat': bounds._southWest.lat,
    'max_lat': bounds._northEast.lat,
    'min_lng': bounds._southWest.lng,
    'max_lng': bounds._northEast.lng,
    'show_gyms': true,
    'show_pokestops': true,
    'show_spawnpoints': true,
    'show_quests': false
  };
  const json = JSON.stringify(data);
  const result = $.ajax({
    url: this.href,
    type: 'POST',
    async: false,
    dataType: 'json',
    data: {'data': json},
    success: function (result) {
      pokestops = result.pokestops;
      spawnpoints = result.spawnpoints;
      gyms = result.gyms;
    }
  });
  return result;
}
async function getSpawnReport(layer, auto) {
  let reportStops = [],
    reportSpawns = [];
  let poly = layer.toGeoJSON();
  let line = turf.polygonToLine(poly);
  const preparedData = await prepareData(layer._bounds)
  preparedData.pokestops.forEach(function(item) {
    point = turf.point([item.lng, item.lat]);
    if (turf.inside(point, poly)) {
      reportStops.push(item.id);
    }
  });
  preparedData.spawnpoints.forEach(function(item) {
    point = turf.point([item.lng, item.lat]);
    if (turf.inside(point, poly)) {
      reportSpawns.push(item.id);
    }
  });
  let srl = auto ? 1 : settings.spawnReportLimit;
  const data = {
    'get_spawndata': true,
    'nest_migration_timestamp': settings.nestMigrationDate,
    'spawn_report_limit': srl,
    'stops': [],
    'spawns': reportSpawns
  };
  const json = JSON.stringify(data);
  const result = await $.ajax({
    url: this.href,
    type: 'POST',
    dataType: 'json',
    data: {'data': json},
  });
  return result;
}
function generateSpawnReport(result, layer) {
  spawnReport = [];
      if (result.spawns !== null) {
        if (result.spawns[0] != undefined) {
          let osmid = layer.tags.osmid;
          let nestPokemon = result.spawns[0].pokemon_id;
          let nowUt = Math.floor(Date.now() / 1000);
          let pkmCount = result.spawns[0].count;
          let avgSpawns = (pkmCount / ((nowUt - settings.nestMigrationDate) / 3600 / 60)).toFixed(2);
          let nestName = layer.tags.name;
          let lat = layer.tags.centerLat;
          let lon = layer.tags.centerLon;
          let path = layer.tags.path;
          spawnReport.push([osmid, nestPokemon, avgSpawns, nowUt, pkmCount, nestName, lat, lon, path])
        } else {
          let osmid = layer.tags.osmid;
          let nestPokemon = 0;
          let nowUt = Math.floor(Date.now() / 1000);
          let pkmCount = 0;
          let avgSpawns = 0;
          let nestName = layer.tags.name;
          let lat = layer.tags.centerLat;
          let lon = layer.tags.centerLon;
          let path = layer.tags.path;
          spawnReport.push([osmid, nestPokemon, avgSpawns, nowUt, pkmCount, nestName, lat, lon, path])
        }
        result.spawns.forEach(function(item) {
          if (typeof layer.tags !== 'undefined') {
            $('#modalSpawnReport  .modal-title').text(subs.spawnReport + layer.tags.name);
          }
          $('#spawnReportTable > tbody:last-child').append('<tr><td>' + pokemon[item.pokemon_id-1] + '</td><td>' + item.count + '</td></tr>');
        });
      } else {
          if (typeof layer.tags !== 'undefined') {
          $('#modalSpawnReport  .modal-title').text(subs.spawnReport + layer.tags.name);
        }
        $('#spawnReportTable > tbody:last-child').append('<tr><td colspan="2">' + subs.noData + '</td></tr>');
      }
      $("#modalSpawnReport .writeNest").text(subs.writeToDB);
      $('#modalSpawnReport').modal('show');
}
function clearAllLayers() {
  bgLayer.clearLayers();
  circleLayer.clearLayers();
  instanceLayer.clearLayers();
  editableLayer.clearLayers();
  admLayer.clearLayers();
  nestLayer.clearLayers();
  subsLayer.clearLayers();
  routingLayer.clearLayers();
  exportList.clearLayers();
  circleS2Layer.clearLayers();
  questLayer.clearLayers();
  bootstrapLayer.clearLayers();
  circleInstance = [];
  instances = [];
}
function getAdBounds(adBoundsLv) {
  clearAllLayers();
  const bounds = map.getBounds();
  const overpassApiEndpoint = 'https://overpass-api.de/api/interpreter';
  let queryBbox = [ // s, e, n, w
    bounds.getSouthWest().lat,
    bounds.getSouthWest().lng,
    bounds.getNorthEast().lat,
    bounds.getNorthEast().lng
  ].join(',');
  let queryDate = new Date().toISOString();
  let queryOptions = [
    '[out:json]',
    '[timeout:620]',
    '[bbox:' + queryBbox + ']',
    '[date:"' + queryDate + '"]'
  ].join('');
  let queryAdBounds = [
    'relation[admin_level=' + adBoundsLv + '];',
  ].join('');
  let overPassQuery = queryOptions + ';(' + queryAdBounds + ')' + ';out;>;out skel qt;';
  $.ajax({
    beforeSend: function() {
      $("#modalLoading").modal('show');
    },
    url: overpassApiEndpoint,
    type: 'GET',
    dataType: 'json',
    data: {'data': overPassQuery},
    success: function (result) {
      let geoJsonFeatures = osmtogeojson(result);
      geoJsonFeatures.features.forEach(function(feature) {
        if (feature.geometry.type == 'Polygon' || feature.geometry.type == 'MultiPolygon') { 
          feature = turf.flip(feature);
          let polygon = L.polygon(feature.geometry.coordinates, {
          clickable: false,
          color: "#a83297",
          fill: true,
          fillColor: '#a83297',
          fillOpacity: 0.1,
          opacity: 1.0,
          stroke: true,
          weight: 2
          });
          polygon.tags = {};
          polygon.tags.name = feature.properties.tags.name;
          polygon.tags.osmid = feature.properties.id;
          polygon.tags.included = false;
          let area = L.GeometryUtil.geodesicArea(polygon.getLatLngs()[0]);
          let readableArea = L.GeometryUtil.readableArea(area, true);
          let name = '';
          let included = '';
          polygon.bindPopup(function (layer) {
            if (typeof layer.tags.name !== 'undefined') {
              name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + layer.tags.name + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
            }
            if (layer.tags.included == true) {
              included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm removeFromExport" data-layer-container="admLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeFromExport + '</span></div></div>';
            } else {
              included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm addToExport" data-layer-container="admLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.addToExport + '</span></div></div>';
            }
            let merge = '';
            if (layer.tags.merged != true) {
              merge = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm mergePolygons" data-layer-container="editableLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.mergePolygons + '</span></div></div>';
            }
            let output = name +
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="admLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeMap + '</span></div></div>' +

                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm exportLayer" data-layer-container="admLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportPolygon + '</span></div></div>' +

                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm exportPoints" data-layer-container="admLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportVP + '</span></div></div>' +

                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm countPoints" data-layer-container="admLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.countVP + '</span></div></div>' +
                  included + merge;
            return output;
          }, {maxWidth: 500, minWidth: 300}).addTo(admLayer);
        }
      });
    },
    complete: function() {
      $("#modalLoading").modal('hide');
    }
  });
}
function getNests(queryNestArgs) {
  clearAllLayers();
  const bounds = map.getBounds();
  const overpassApiEndpoint = 'https://overpass-api.de/api/interpreter';
  let queryBbox = [ // s, e, n, w
    bounds.getSouthWest().lat,
    bounds.getSouthWest().lng,
    bounds.getNorthEast().lat,
    bounds.getNorthEast().lng
  ].join(',');
  let queryDate = "2019-02-16T00:00:00Z";
  let queryOptions = [
    '[out:json]',
    '[bbox:' + queryBbox + ']',
    '[date:"' + queryDate + '"]'
  ].join('');
  let overPassQuery = queryOptions + ';(' + queryNestArgs + ')' + ';out;>;out skel qt;';
  $.ajax({
    beforeSend: function() {
      $("#modalLoading").modal('show');
    },
    url: overpassApiEndpoint,
    type: 'GET',
    dataType: 'json',
    data: {'data': overPassQuery},
    success: function (result) {
      let geoJsonFeatures = osmtogeojson(result);
      geoJsonFeatures.features.forEach(function(feature) {
        feature = turf.flip(feature);
        let polygon = L.polygon(feature.geometry.coordinates, {
          clickable: false,
          color: "#ff8833",
          fill: true,
          fillColor: null,
          fillOpacity: 0.2,
          opacity: 0.5,
          stroke: true,
          weight: 4
        });
        polygon.tags = {};
        polygon.tags.name = feature.properties.tags.name;
        polygon.tags.osmid = feature.properties.id;
        polygon.tags.included = false;
        let area = L.GeometryUtil.geodesicArea(polygon.getLatLngs()[0]);
        let readableArea = L.GeometryUtil.readableArea(area, true);
        polygon.addTo(nestLayer);
        let name = '';
        let nameInput = '';
        let included = '';
        polygon.bindPopup(function (layer) {
          if (typeof layer.tags.name !== 'undefined') {
            name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + subs.nest + ': ' + layer.tags.name + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
            nameInput = '<hr>';
          } else {
            name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">' + subs.polygon + '</span></div>' + '<div class="input-group mb-3">' + subs.area + ': ' + readableArea + '</div>';
            nameInput = '<hr><div class="input-group mb-3">' +
                              '<div class="input-group-prepend">' +
                                '<span class="input-group-text">' + subs.name + '</span>' +
                              '</div>' +
                              '<input id="polygonName" name="polygonName" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="text" class="form-control" aria-label="Polygon name">' +
                            '</div>';
          }
          if (layer.tags.included == true) {
            included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm removeFromExport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeFromExport + '</span></div></div>';
          } else {
            included = '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm addToExport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id + ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.addToExport + '</span></div></div>';
          }
          let output = name +
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm getSpawnReport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.getSpawnReport + '</span></div></div>' +
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.removeMap + '</span></div></div>' +
                  '<div class="input-group"><button class="btn btn-secondary btn-sm exportLayer" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">' + subs.exportPolygon + '</span></div></div>' +
                  nameInput + included;
          return output;
        }, {maxWidth: 500, minWidth: 300});
      });
    },
    complete: function() {
      $("#modalLoading").modal('hide');
    }
  });
}
function splitCsv(str){
  let result = [];
  let strBuf = '';
  let start = 0 ;
  let marker = false;
  let i;
  for (i = 0; i< str.length; i++){

    if (str[i] === '"'){
      marker = !marker;
    }
    if (str[i] === ',' && !marker){
      result.push(str.substr(start, i - start));
      start = i+1;
    }
  }
  if (start <= str.length){
    result.push(str.substr(start, i - start));
  }
  for (let r = 0; r < result.length; r++) {
    for (let j = 0; j < result[r].length; j++) {
      if (result[r][j] === '"') {
        result[r] = result[r].slice(1,-1);
      }
    }
  }
  return result;
};
function csvtoarray(dataString, wf = false) {
  let lines = dataString
    .split(/\n/)           // Convert to one string per line
    .map(function(lineStr) {
      if (wf == true) {
        return splitCsv(lineStr);   // Convert considering ("") 
      } else {
        return lineStr.split(",");   // Convert each line to array (,)
      }
    })
  return lines;
}
function loadData() {
  const bounds = map.getBounds();
  const data = {
    'get_data': true,
    'min_lat': bounds.getSouthWest().lat,
    'max_lat': bounds.getNorthEast().lat,
    'min_lng': bounds.getSouthWest().lng,
    'max_lng': bounds.getNorthEast().lng,
    'show_gyms': settings.showGyms,
    'show_pokestops': settings.showPokestops,
    'show_spawnpoints': settings.showSpawnpoints,
    'show_quests': settings.showMissingQuests
  };
  const json = JSON.stringify(data);
  $.ajax({
    url: this.href,
    type: 'POST',
    dataType: 'json',
    data: {'data': json},
    success: function (result) {
      pokestopLayer.clearLayers();
      pokestopRangeLayer.clearLayers();
      gymLayer.clearLayers();
      spawnpointLayer.clearLayers();
      gyms = [];
      pokestops = [];
      pokestoprange = [];
      spawnpoints = [];
      spawnpoints_u = [];
      if (result.gyms != null && settings.showGyms === true) {
        result.gyms.forEach(function(item) {
          gyms.push(item);
          if (settings.showUnknownPois == false) {  
            let lastUpdate = new Date(item.updated*1000).toUTCString().slice(4,-4);
            let radius = (6/8) + ((7/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            let weight = (1/8) + ((1/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            if(item.ex == 1){
              let marker = L.circleMarker([item.lat, item.lng], {
              color: 'black',
              fillColor: 'maroon',
              radius: radius,
              weight: weight,
              opacity: 1,
              fillOpacity: 0.8
            }).addTo(map);
            marker.tags = {};
            marker.tags.id = item.id;
            marker.bindPopup("<span>ID: " + item.id + "<br>" + item.name + subs.exEligible + "<br>" + subs.lastUpdate + lastUpdate + "</span>").addTo(gymLayer);
            }
            else{
              let marker = L.circleMarker([item.lat, item.lng], {
              color: 'black',
              fillColor: 'orange',
              radius: radius,
              weight: weight,
              opacity: 1,
              fillOpacity: 0.8
            }).addTo(map);
            marker.tags = {};
            marker.tags.id = item.id;
            marker.bindPopup("<span>ID: " + item.id + "<br>" + item.name + "<br>" + subs.lastUpdate + lastUpdate + "</span>").addTo(gymLayer);
            }
          } else if (settings.showUnknownPois == true && item.name == null) {
            let lastUpdate = new Date(item.updated*1000).toUTCString().slice(4,-4);
            let radius = (6/8) + ((7/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            let weight = (1/8) + ((1/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            if(item.ex == 1){
              let marker = L.circleMarker([item.lat, item.lng], {
              color: 'black',
              fillColor: 'maroon',
              radius: radius,
              weight: weight,
              opacity: 1,
              fillOpacity: 0.8
            }).addTo(map);
            marker.tags = {};
            marker.tags.id = item.id;
            marker.bindPopup("<span>ID: " + item.id + "<br>" + item.name + subs.exEligible + "<br>" + subs.lastUpdate + lastUpdate + "</span>").addTo(gymLayer);
            }
            else{
              let marker = L.circleMarker([item.lat, item.lng], {
              color: 'black',
              fillColor: 'orange',
              radius: radius,
              weight: weight,
              opacity: 1,
              fillOpacity: 0.8
            }).addTo(map);
            marker.tags = {};
            marker.tags.id = item.id;
            marker.bindPopup("<span>ID: " + item.id + "<br>" + item.name + "<br>" + subs.lastUpdate + lastUpdate + "</span>").addTo(gymLayer);
            }
          }
        });
      }
      if (result.pokestops != null && settings.showPokestops === true) {
        result.pokestops.forEach(function(item) {
          if (item.deleted != 1) {
          if (settings.showUnknownPois == false) {
            pokestops.push(item);
            let lastUpdate = new Date(item.updated*1000).toUTCString().slice(4,-4);
            let radius = (6/8) + ((6/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            let weight = (1/8) + ((1/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            let marker = L.circleMarker([item.lat, item.lng], {
              color: 'black',
              fillColor: 'green',
              radius: radius,
              weight: weight,
              opacity: 1,
              fillOpacity: 0.8
            }).addTo(map);
            marker.tags = {};
            marker.tags.id = item.id;
            marker.bindPopup("<span>ID: " + item.id + "<br>" + item.name + "<br>" + subs.lastUpdate + lastUpdate + "</span>").addTo(pokestopLayer);
          } else if (settings.showUnknownPois == true && item.name == null) {
            pokestops.push(item);
            let lastUpdate = new Date(item.updated*1000).toUTCString().slice(4,-4);
            let radius = (6/8) + ((6/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            let weight = (1/8) + ((1/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            let marker = L.circleMarker([item.lat, item.lng], {
              color: 'black',
              fillColor: 'green',
              radius: radius,
              weight: weight,
              opacity: 1,
              fillOpacity: 0.8
            }).addTo(map);
            marker.tags = {};
            marker.tags.id = item.id;
            marker.bindPopup("<span>ID: " + item.id + "<br>" + item.name + "<br>" + subs.lastUpdate + lastUpdate + "</span>").addTo(pokestopLayer);
          }
          }
        });
      }
      if (result.pokestops != null && settings.showPokestopsRange === true && settings.showUnknownPois == false) {
        result.pokestops.forEach(function(item) {
          if (item.deleted != 1) {
            pokestoprange.push(item);
            let marker = L.circle([item.lat, item.lng], {
              color: 'green',
              radius: 70,
              opacity: 0.2
            }).addTo(map);
            marker.tags = {};
            marker.tags.id = item.id;
            marker.bindPopup("<span>ID: " + item.id + "</span>").addTo(pokestopRangeLayer);
          }
        });
      } else if (result.pokestops != null && settings.showPokestopsRange === true && settings.showUnknownPois == true) {
        result.pokestops.forEach(function(item) {
          if (item.deleted != 1 && item.name == null) {
            pokestoprange.push(item);
            let marker = L.circle([item.lat, item.lng], {
              color: 'green',
              radius: 70,
              opacity: 0.2
            }).addTo(map);
            marker.tags = {};
            marker.tags.id = item.id;
            marker.bindPopup("<span>ID: " + item.id + "</span>").addTo(pokestopRangeLayer);
          }
        });
      }
      if (result.spawnpoints != null && settings.showSpawnpoints === true) {
        if (settings.hideOldSpawnpoints == true){ 
          let oldSpawnpointsTimestamp = settings.oldSpawnpointsTimestamp;
          result.spawnpoints.forEach(function(item) {
            if (item.despawn_sec != null && item.updated >= oldSpawnpointsTimestamp) {
              spawnpoints.push(item);
            } else if (item.updated >= oldSpawnpointsTimestamp){
              spawnpoints_u.push(item);
              spawnpoints.push(item);
            }
            let radius = (6/8) + ((4/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            let weight = (1/8) + ((1/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            if (settings.showUnknownPois == false){
              if (item.despawn_sec != null && item.updated >= oldSpawnpointsTimestamp) {
                let marker = L.circleMarker([item.lat, item.lng], {
                  color: 'black',
                  fillColor: 'blue',
                  radius: radius,
                  weight: weight,
                  opacity: 1,
                  fillOpacity: 0.8
                }).addTo(map);
                marker.tags = {};
                marker.tags.id = item.id;
                let despawn_time = new Date(parseInt(item.despawn_sec)*1000).toISOString().slice(-10, -5);
                marker.bindPopup("<span>ID: " + item.id + "</span>\n" + subs.despawnTime + despawn_time).addTo(spawnpointLayer);
              } else if (item.despawn_sec == null && item.updated >= oldSpawnpointsTimestamp) {
                let marker = L.circleMarker([item.lat, item.lng], {
                  color: 'black',
                  fillColor: 'red',
                  radius: radius,
                  weight: weight,
                  opacity: 1,
                  fillOpacity: 0.8
                }).addTo(map);
                marker.tags = {};
                marker.tags.id = item.id;
                marker.bindPopup("<span>ID: " + item.id + "</span>\n" + subs.unknownDespawnTime).addTo(spawnpointLayer);
              }
            } else if (settings.showUnknownPois == true && item.despawn_sec == null && item.updated >= oldSpawnpointsTimestamp){
              let marker = L.circleMarker([item.lat, item.lng], {
                  color: 'black',
                  fillColor: 'red',
                  radius: radius,
                  weight: weight,
                  opacity: 1,
                  fillOpacity: 0.8
                }).addTo(map);
                marker.tags = {};
                marker.tags.id = item.id;
                marker.bindPopup("<span>ID: " + item.id + "</span>\n" + subs.unknownDespawnTime).addTo(spawnpointLayer);
            }
          });
        } else {
          result.spawnpoints.forEach(function(item) {
            if (item.despawn_sec != null) {
              spawnpoints.push(item);
            } else {
              spawnpoints_u.push(item);
              spawnpoints.push(item);
            }
            let radius = (6/8) + ((4/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            let weight = (1/8) + ((1/8) * (map.getZoom() - 11)) // Depends on Zoomlevel
            if (settings.showSpawnpoints === true && settings.showUnknownPois == false) {
              if (item.despawn_sec != null){
                let marker = L.circleMarker([item.lat, item.lng], {
                  color: 'black',
                  fillColor: 'blue',
                  radius: radius,
                  weight: weight,
                  opacity: 1,
                  fillOpacity: 0.8
                }).addTo(map);
                marker.tags = {};
                marker.tags.id = item.id;
                let despawn_time = new Date(parseInt(item.despawn_sec)*1000).toISOString().slice(-10, -5);
                marker.bindPopup("<span>ID: " + item.id + "</span>\n" + subs.despawnTime + despawn_time).addTo(spawnpointLayer);
              } else {
                let marker = L.circleMarker([item.lat, item.lng], {
                  color: 'black',
                  fillColor: 'red',
                  radius: radius,
                  weight: weight,
                  opacity: 1,
                  fillOpacity: 0.8
                }).addTo(map);
                marker.tags = {};
                marker.tags.id = item.id;
                marker.bindPopup("<span>ID: " + item.id + "</span>\n" + subs.unknownDespawnTime).addTo(spawnpointLayer);
              }
            } else if (settings.showSpawnpoints === true && settings.showUnknownPois == true && item.despawn_sec == null) {
                let marker = L.circleMarker([item.lat, item.lng], {
                  color: 'black',
                  fillColor: 'red',
                  radius: radius,
                  weight: weight,
                  opacity: 1,
                  fillOpacity: 0.8
                }).addTo(map);
                marker.tags = {};
                marker.tags.id = item.id;
                marker.bindPopup("<span>ID: " + item.id + "</span>\n" + subs.unknownDespawnTime).addTo(spawnpointLayer);
            }
          });
        }
      }
    }
  });
  updateS2Overlay()
}
function lastNestChange() {
  let reference = 1599091200 * 1000;
  let actual = Date.now();
  let diff = actual - reference;
  do {
    diff -= 1209600000;
  } while (diff > 0);
  let result = (actual - (1209600000 - Math.abs(diff))) / 1000;
  return result;
}
$(document).ready(function() {
  $('input[type=radio][name=exportPolygonDataType]').change(function() {
    if (this.value == 'exportPolygonDataTypeCoordsList') {
      $('#exportPolygonDataCoordsList').show();
      $('#exportPolygonDataGeoJson').hide();
      $('#exportPolygonDataPoracle').hide();
      copyOutput = 'exportPolygonDataCoordsList';
      $(document.getElementById('copyPolygonOutput')).text(subs.copyClipboard);
    } else if (this.value == 'exportPolygonDataTypeGeoJson') {
      $('#exportPolygonDataCoordsList').hide();
      $('#exportPolygonDataGeoJson').show();
      $('#exportPolygonDataPoracle').hide();
      copyOutput = 'exportPolygonDataGeoJson';
      $(document.getElementById('copyPolygonOutput')).text(subs.copyClipboard);
    } else if (this.value == 'exportPolygonDataTypePoracle') {
      $('#exportPolygonDataCoordsList').hide();
      $('#exportPolygonDataGeoJson').hide();
      $('#exportPolygonDataPoracle').show();
      copyOutput = 'exportPolygonDataPoracle';
      $(document.getElementById('copyPolygonOutput')).text(subs.copyClipboard);
    }
  });
  $(document).on("click", ".sortInstance", function() {
    let container = $(this).attr('data-layer-container');
    let id = $(this).attr('data-layer-id');
    let sourceCircle;
    let sourceLayer = [];
    let oldInstance;
    if (container == 'circleLayer') {
      sourceLayer = circleLayer;
    } else if (container == 'bootstrapLayer') {
      sourceLayer = bootstrapLayer;
    }
    if (container == 'instanceLayer') {
      sourceCircle = instanceLayer.getLayer(parseInt(id));
      oldInstance = instances[sourceCircle.options.instanceID];
    } else {
      let instance = [];
      instance.id = instances.length;
      instance.name = subs.drawnCircles + ' ' + instance.id;
      sourceCircle = sourceLayer.getLayer(parseInt(id));
      sourceCircle.options.instanceID = instance.id;
      let radius = sourceCircle.getRadius();
      let color = sourceCircle.options.color;
      sourceLayer.getLayers().forEach(function(item) {
        let newLayer;
        circle = L.circle([item.getLatLng().lat, item.getLatLng().lng], {
          route_counter: sourceLayer.length,
          route_length: sourceLayer.routeLength,
          color: color,
          fillOpacity: 0.2,
          draggable: true,
          radius: radius,
          instanceID: instance.id
        }).bindPopup(function (layer) {
          return getCircleHtml(instance.name, layer, subs);
        }).addTo(instanceLayer);
        sourceLayer.removeLayer(parseInt(item._leaflet_id));
        instance.push(circle._leaflet_id);
      });
      instances[sourceCircle.options.instanceID-1] = [];
      instances.push(instance);
      oldInstance = instance;
    }
    let allCircles = [];
    oldInstance.forEach(function(item) {
      circle = instanceLayer.getLayer(item);
      allCircles.push(circle);
    });
    allCircles.forEach(function(item) {
      instanceLayer.removeLayer(item._leaflet_id);
    })
    instances[sourceCircle.options.instanceID] = [];
    let newInstance = [];
    newInstance.name = oldInstance.name;
    newInstance.id = instances.length;
    let radius = sourceCircle.getRadius();
    let color = sourceCircle.options.color;
    $.ajax({
      beforeSend: function() {
        $("#modalLoading").modal('show');
      },
      success: function() {
        let points = [];
        for (i=0;i<allCircles.length;i++) {
          let circle = allCircles[i].getLatLng();
          let Point = {
            x: circle.lat,
            y: circle.lng
          }
          points.push(Point);
        };
        let temp1 = '9'.repeat(((allCircles.length).toString().length)-1);
        let temp2 = Math.ceil(allCircles.length/10);
        let temp_coeff = '0.99999' + temp1 + temp2;
        let solution = solve(points, temp_coeff); 
        let orderedPoints = solution.map(i => points [i]);
        let distanceAll = 0;
          for (i=0;i<points.length-1;i++) {
            let pointA = L.point(orderedPoints[i].x, orderedPoints[i].y);
            let pointB = L.point(orderedPoints[i+1].x, orderedPoints[i+1].y);
            let distance = pointA.distanceTo(pointB)*100;
            distanceAll += distance;
          }
        let routeLength = distanceAll.toFixed(3);
        let avgDistance = (distanceAll/orderedPoints.length*1000).toFixed(2);
        console.log('average distance: ' + avgDistance + 'm')
        orderedPoints.forEach(function(item) {
          newCircle = L.circle([item.x, item.y], {
            route_counter: allCircles.length,
            route_length: routeLength,
            color: color,
            fillOpacity: 0.2,
            draggable: true,
            radius: radius,
            instanceID: newInstance.id
          }).bindPopup(function (layer) {
            return getCircleHtml(newInstance.name, layer, subs);
          }).addTo(instanceLayer);
          newInstance.push(newCircle._leaflet_id);  
        });
        instances.push(newInstance);
        drawRoute(newInstance);
        instanceLayer.bringToFront();
      },
      complete: function() {
        $("#modalLoading").modal('hide');
      }
    });
  })
  $(document).on('click', '#showMissingQuests', function() {
    let selection = $('#multiQuest').multi_select('getSelectedValues');
    let choice = [];
    selection.forEach(function(item){
      let x = myQuestSelect[item];
      choice.push(x)
    });
    showMissingQuests(choice);
    setShowMode();
    $('#modalQuestInstances').modal('hide')
  })
  $('#getOutput').click(function() {
    if (($('#multiInstances').multi_select('getSelectedValues')).length > 0) {
      $('#outputCircles').val('');
      let allCircles = getAllCircles().getLayers();
      let avgPt = 0;
      let exportType = $("#modalOutput input[name=exportCoordsType]:checked").val()
      if (exportType == 'sorted') {
        $.ajax({
          beforeSend: function() {
            $("#modalLoading").modal('show');
          },
          success: function() {
            let points = [];
            for (i=0;i<allCircles.length;i++) {
              let circle = allCircles[i].getLatLng();
              let Point = {
                x: circle.lat,
                y: circle.lng
              }
              points.push(Point);
            };
            let temp1 = '9'.repeat(((allCircles.length).toString().length)-1);
            let temp2 = Math.ceil(allCircles.length/10);
            let temp_coeff = '0.99999' + temp1 + temp2;
            let solution = solve(points, temp_coeff); 
            let orderedPoints = solution.map(i => points [i]); 
            for (i=0;i<orderedPoints.length;i++) {
              $('#outputCircles').val(function(index, text) {
                if (i != orderedPoints.length-1) {
                  return text + (orderedPoints[i].x + "," + orderedPoints[i].y) + "\n" ;
                }
                return text + (orderedPoints[i].x + "," + orderedPoints[i].y);
              });
            }
            $('#outputCirclesCount').val(allCircles.length);
            avgPt = (countPointsInCircles()) / (allCircles.length);
            $('#outputAvgPt').val(avgPt.toFixed(2));
          },
          complete: function() {
            $("#modalLoading").modal('hide');
          }
        });
      } else {
        for (i=0;i<allCircles.length;i++) {
          let circleLatLng = allCircles[i].getLatLng();
          $('#outputCircles').val(function(index, text) {
            if (i != allCircles.length-1) {
              return text + (circleLatLng.lat + "," + circleLatLng.lng) + "\n" ;
            }
            return text + (circleLatLng.lat + "," + circleLatLng.lng);
          });
        }
        $('#outputCirclesCount').val(allCircles.length);
        avgPt = (countPointsInCircles()) / (allCircles.length);
        $('#outputAvgPt').val(avgPt.toFixed(2));
      }
    } else {
      $('#outputCirclesCount').val('n/a');
      console.log('No instances chosen')
    }
  });
});
$(document).on("click", ".deleteLayer", function() {
  let id = $(this).attr('data-layer-id');
  let container = $(this).attr('data-layer-container');
  switch (container) {
    case 'circleLayer':
      circleLayer.removeLayer(parseInt(id));
      break;
    case 'instanceLayer':
      instanceLayer.removeLayer(parseInt(id));
      break;
    case 'editableLayer':
      editableLayer.removeLayer(parseInt(id));
      break;
    case 'nestLayer':
      nestLayer.removeLayer(parseInt(id));
      break;
    case 'subsLayer':
      let markerID = parseInt(id);
      let rangeID = subsLayer.getLayer(markerID).rangeID;
      subsLayer.removeLayer(markerID);
      subsLayer.removeLayer(rangeID);
      break;
    case 'bootstrapLayer':
      bootstrapLayer.removeLayer(parseInt(id));
      break;
    case 'admLayer':
      admLayer.removeLayer(parseInt(id));
      break;
  }
});
$(document).on('keyup', '#polygonName', function(event) { 
  let id = $(this).attr('data-layer-id');
  let layer;
  let lG;
  let container = $(this).attr('data-layer-container');
  switch (container) {
    case 'editableLayer':
      layer = editableLayer.getLayer(parseInt(id));
      lG = editableLayer;
      break;
    case 'nestLayer':
      layer = nestLayer.getLayer(parseInt(id));
      lG = nestLayer;
      break;
  }
  if (event.keyCode === 13) {
    if (layer.tags.name == undefined || layer.tags.name == '') {
      let newName = $('#polygonName').val();
      if (newName == '') {
        alert(subs.chooseName);
        return false;
      } else {
        layer.tags.name = newName;
      }
    }
    lG.removeFrom(map).addTo(map);
  } 
});
$(document).on("click", ".addToExport", function() {
  let id = $(this).attr('data-layer-id');
  let layer;
  let lG;
  let container = $(this).attr('data-layer-container');
  switch (container) {
    case 'editableLayer':
      layer = editableLayer.getLayer(parseInt(id));
      lG = editableLayer;
      break;
    case 'nestLayer':
      layer = nestLayer.getLayer(parseInt(id));
      lG = nestLayer;
      break;
    case 'admLayer':
      layer = admLayer.getLayer(parseInt(id));
      lG = admLayer;
      break;  
  }
  if ((layer.tags.name == undefined && container == 'nestLayer') || (layer.tags.name == '' && container == 'editableLayer') || (layer.tags.name == '' && container == 'admLayer')) {
    let newName = $('#polygonName').val();
    if (newName == '') {
      alert(subs.chooseName);
      return false;
    } else {
      layer.tags.name = newName;
    }
  }
  exportList.addLayer(layer);
  layer.options.color = '#0c6602';
  layer.tags.included = true;
  lG.removeFrom(map).addTo(map);
  exportListCount = exportList.getLayers().length;
  $('#exportListCount').text(exportListCount);
});
$(document).on("click", ".removeFromExport", function() {
  let id = $(this).attr('data-layer-id');
  let layer;
  let lG;
  let container = $(this).attr('data-layer-container');
  switch (container) {
    case 'editableLayer':
      layer = editableLayer.getLayer(parseInt(id));
      lG = editableLayer;
      break;
    case 'nestLayer':
      layer = nestLayer.getLayer(parseInt(id));
      lG = nestLayer;
      break;
    case 'admLayer':
      layer = admLayer.getLayer(parseInt(id));
      lG = admLayer;
      break;
  }
  exportList.removeLayer(layer);
  layer.options.color = '#ff8833';
  layer.tags.included = false;
  lG.removeFrom(map).addTo(map);
  exportListCount = exportList.getLayers().length;
  $('#exportListCount').text(exportListCount);
});
$(document).on("click", ".getSpawnReport", async function() {
  let id = $(this).attr('data-layer-id');
  let layer;
  let container = $(this).attr('data-layer-container');
  switch (container) {
    case 'editableLayer':
      layer = editableLayer.getLayer(parseInt(id));
      break;
    case 'nestLayer':
      layer = nestLayer.getLayer(parseInt(id));
      break;
  }
  const srData = await getSpawnReport(layer)
  generateSpawnReport(srData, layer);
});
$(document).on("click", "#importNestsOSM", function() {
  let queryNestArgs = '';
  if ($('#osmOption1').is(':checked')) {
    queryNestArgs += 'way["leisure"="park"];relation["leisure"="park"];';
  }
  if ($('#osmOption2').is(':checked')) {
    queryNestArgs += 'way["landuse"="meadow"];relation["landuse"="meadow"];';
  }
  if ($('#osmOption3').is(':checked')) {
    queryNestArgs += 'way["leisure"="recreation_ground"];relation["leisure"="recreation_ground"];way["landuse"="recreation_ground"];relation["landuse"="recreation_ground"];';
  }
  if ($('#osmOption4').is(':checked')) {
    queryNestArgs += 'way["landuse"="grass"];relation["landuse"="grass"];';
  }
  if ($('#osmOption5').is(':checked')) {
    queryNestArgs += 'way["leisure"="pitch"];relation["leisure"="pitch"];';
  }
  if ($('#osmOption6').is(':checked')) {
    queryNestArgs += 'way["leisure"="golf_course"];relation["leisure"="golf_course"];';
  }
  if ($('#osmOption7').is(':checked')) {
    queryNestArgs += 'way["leisure"="playground"];relation["leisure"="playground"];';
  }
  getNests(queryNestArgs);
});
$(document).on("click", ".writeNest", function() {
  if (spawnReport != undefined) {
    spawnReport.forEach(function(item) {
      let nestId = item[0];
      let nestPokemon = item[1];
      let avgSpawns = item[2];
      let nowUt = item[3];
      let pkmCount = item[4];
      let nestName = item[5];
      let lat = item[6];
      let lon = item[7];
      let path = item[8];
    writeNests(nestId, nestPokemon, avgSpawns, nowUt, pkmCount, nestName, lat, lon, path)
    });
    spawnReport = [];
  }
});
$(document).on("click", "#importNests", function() {
  importNests()
});
$(document).on("click", "#updateDb", function() {
  nestLayer.eachLayer(async function(item) {
    let auto = true;
    let result = await getSpawnReport(item, auto);
    if (result.spawns !== null) {
      if (result.spawns[0] != undefined) {
          let nestId = item.tags.osmid;
          let nestPokemon = result.spawns[0].pokemon_id;
          let nowUt = Math.floor(Date.now() / 1000);
          let pkmCount = result.spawns[0].count;
          let avgSpawns = (pkmCount / ((nowUt - settings.nestMigrationDate) / 3600 / 60)).toFixed(2);
          let nestName = item.tags.name;
          let lat = item.tags.centerLat;
          let lon = item.tags.centerLon;
          let path = item.tags.path;
          writeNests(nestId, nestPokemon, avgSpawns, nowUt, pkmCount, nestName, lat, lon, path)
          console.log(nestName)
      } else {
          let nestId = item.tags.osmid;
          let nestPokemon = 0;
          let nowUt = Math.floor(Date.now() / 1000);
          let pkmCount = 0;
          let avgSpawns = 0;
          let nestName = item.tags.name;
          let lat = item.tags.centerLat;
          let lon = item.tags.centerLon;
          let path = item.tags.path;
          writeNests(nestId, nestPokemon, avgSpawns, nowUt, pkmCount, nestName, lat, lon, path)
          console.log(nestName)
      }     
    }
  });
  $("#modalNests .updateButton").text(subs.writeSuccess);
});
function showMissingQuests(choice) {
  if (choice.length > 0) {
    settings.showMissingQuests = true;
  }
  choice.forEach(function(item) {
    const data = {
      'get_instance_data': true,
      'instance_name': item
    };
    const json = JSON.stringify(data);
    let weight;
    if ($('#instanceBorders').is(':checked')) {
      weight = 2;
    } else {
      weight = 0;
    }
    let polygonOptions = {
      clickable: false,
      color: 'black',
      fill: true,
      fillColor: null,
      fillOpacity: 0.0,
      opacity: 1.0,
      stroke: true,
      weight: weight
    };
    $.ajax({
      url: this.href,
      type: 'POST',
      async: false,
      dataType: 'json',
      data: {'data': json},
      success: function (result) {
        points = result.data.area;
        if (points.length > 0 ) {       
          points.forEach(function(coords) {
            newPolygon = L.polygon(coords, polygonOptions).addTo(questLayer);
          });
        }
      }
    });
  })
  let bounds = questLayer.getBounds();
  const data = {
    'get_data': true,
    'min_lat': bounds.getSouthWest().lat,
    'max_lat': bounds.getNorthEast().lat,
    'min_lng': bounds.getSouthWest().lng,
    'max_lng': bounds.getNorthEast().lng,
    'show_gyms': settings.showGyms,
    'show_pokestops': settings.showPokestops,
    'show_spawnpoints': settings.showSpawnpoints,
    'show_quests': settings.showMissingQuests
  };
  const json = JSON.stringify(data);
  $.ajax({
    url: this.href,
    type: 'POST',
    async: false,
    dataType: 'json',
    data: {'data': json},
    success: function (result) {
      if (result.quests != null && settings.showMissingQuests === true) {
        questLayer.getLayers().forEach(function(layer) {
          let poly = layer.toGeoJSON();
          let line = turf.polygonToLine(poly);
          result.quests.forEach(function(item) {
            let stop_deleted = (item.deleted == 1) ? '<br>' + subs.stop_deleted : '';
            point = turf.point([item.lng, item.lat]);
            if (turf.inside(point, poly)) {
              let radius = (6/8) + ((8/8) * (map.getZoom() - 9)) // Depends on Zoomlevel
              let weight = (1/8) + ((1/8) * (map.getZoom() - 10)) // Depends on Zoomlevel
              let marker = L.circleMarker([item.lat, item.lng], {
                color: 'black',
                fillColor: 'red',
                radius: radius,
                weight: weight,
                opacity: 1,
                fillOpacity: 0.8
              }).addTo(map);
              marker.tags = {};
              marker.tags.id = item.id;
              marker.bindPopup("<span>ID: " + item.id + "<br>" + item.name + stop_deleted + "</span>").addTo(questLayer);
            }
          });
        });
      }
    },
    error: function () {
      alert('Something went horribly wrong');
    }
  });
}
function getAllCircles() {
  let allCircles = new L.FeatureGroup();
  let instancesIncluded = [];
  let choice = $('#multiInstances').multi_select('getSelectedValues');
  choice.forEach(function(item){
    let x = mySelect[item];
    for (i = 0; i < instances.length; i++) {
      if (instances[i].name == x) {
        instancesIncluded.push(instances[i].id)
      }
    }
  });
  instancesIncluded.forEach(function(instance) {
    instances[instance].forEach(function(id) {
      let layer = [];
      if (circleLayer.getLayer(id) != undefined) {
        layer = circleLayer.getLayer(id);
      } else if (instanceLayer.getLayer(id) != undefined) {
        layer = instanceLayer.getLayer(id);
      } else if (bootstrapLayer.getLayer(id) != undefined) {
        layer = bootstrapLayer.getLayer(id);
      }
      if (layer.length != 0) {
        layer.addTo(allCircles);
      }
    });
  });
  return allCircles;
}
function countPointsInCircles(display) {
  let allCircles = getAllCircles();
  let bounds = [];
  if (allCircles != undefined) {
    bounds = allCircles.getBounds();
  } else {
    bounds = map.getBounds();
  }
  prepareData(bounds);
  let count = 0;
  let includedGyms = [];
  let includedStops = [];
  let includedSpawnpoints = [];
  allCircles.eachLayer(function(layer){
    let radius = layer.getRadius();
    let circleCenter = layer.getLatLng();  
    if (settings.showGyms == true) {
      gyms.forEach(function(item) {
        let point =  L.latLng(item.lat,item.lng);
        if(circleCenter.distanceTo(point) <= radius && includedGyms.indexOf(item) === -1){
          count++;
          includedGyms.push(item);
        }
      });
    }
    if (settings.showPokestops == true) {
      pokestops.forEach(function(item) {
        let point =  L.latLng(item.lat,item.lng);
        if(circleCenter.distanceTo(point) <= radius && includedStops.indexOf(item) === -1){
          count++;
          includedStops.push(item);
        }
      });
    }
    if (settings.showSpawnpoints == true) {
      spawnpoints.forEach(function(item) {
        let point =  L.latLng(item.lat,item.lng);
        if(circleCenter.distanceTo(point) <= radius && includedSpawnpoints.indexOf(item) === -1){
          count++;
          includedSpawnpoints.push(item);
        }
      });
    }
  });
  if (display == true) {
    alert(subs.countTotal + count + '\n' + subs.countGyms + includedGyms.length + '\n' + subs.countStops + includedStops.length + '\n' + subs.countSpawnpoints + includedSpawnpoints.length);
  }
  return count;
}
$(document).on("click", "#getCirclesCount", function() {
  let display = true;
  countPointsInCircles(display);
});
$(document).ready(getLanguage());
$(document).on("click", "#getAllNests", function() {     
  $.when($("#modalLoading").modal('show')).then(async function() {
    const preparedData = await prepareData(nestLayer.getBounds());
    nestLayer.eachLayer(async function(layer) {
      let reportStops = [],
      reportSpawns = [];
      let center = layer.getBounds().getCenter()
      let poly = layer.toGeoJSON();
      let line = turf.polygonToLine(poly);
      preparedData.pokestops.forEach(function(item) {
        point = turf.point([item.lng, item.lat]);
        if (turf.inside(point, poly)) {
          reportStops.push(item.id);
        }
      });
      preparedData.spawnpoints.forEach(function(item) {
        point = turf.point([item.lng, item.lat]);
        if (turf.inside(point, poly)) {
          reportSpawns.push(item.id);
        }
      });
      const data = {
        'get_spawndata': true,
        'nest_migration_timestamp': settings.nestMigrationDate,
        'spawn_report_limit': settings.spawnReportLimit,
        'stops': [],
        'spawns': reportSpawns
      };
      const json = JSON.stringify(data);
      const result = Promise.resolve($.ajax({
        url: this.href,
        type: 'POST',
        dataType: 'json',
        data: {'data': json},
        error: function(error) {
          console.log("meh");
        }
      })).then(e => {
        generateAllNestReports(e, layer);
      });
    });
    $('#modalOutput').modal('hide');
    $('#modalSpawnReport .modal-title').text(subs.nestReport);
    $("#modalSpawnReport .writeNest").text(subs.writeToDB);
    $('#modalSpawnReport').modal('show');
    
  }).then(function() {
    $("#modalLoading").modal('hide');
  });
});
function generateAllNestReports(result, layer) {
  $("#modalSpawnReport .writeNest").text(subs.writeToDB);
      if (result.spawns !== null) {
        if (result.spawns[0] != undefined) {
          let osmid = layer.tags.osmid;
          let nestPokemon = result.spawns[0].pokemon_id;
          let nowUt = Math.floor(Date.now() / 1000);
          let pkmCount = result.spawns[0].count;
          let avgSpawns = (pkmCount / ((nowUt - settings.nestMigrationDate) / 3600 / 60)).toFixed(2);
          let nestName = layer.tags.name;
          let lat = layer.tags.centerLat;
          let lon = layer.tags.centerLon;
          let path = layer.tags.path;
          spawnReport.push([osmid, nestPokemon, avgSpawns, nowUt, pkmCount, nestName, lat, lon, path])
        } else {
          let osmid = layer.tags.osmid;
          let nestPokemon = 0;
          let nowUt = Math.floor(Date.now() / 1000);
          let pkmCount = 0;
          let avgSpawns = 0;
          let nestName = layer.tags.name;
          let lat = layer.tags.centerLat;
          let lon = layer.tags.centerLon;
          let path = layer.tags.path;
          spawnReport.push([osmid, nestPokemon, avgSpawns, nowUt, pkmCount, nestName, lat, lon, path])
        }
      }
  if (result.spawns !== null) {
            if (typeof layer.tags.name !== 'undefined') {
              $('#spawnReportTable > tbody:last-child').append('<tr><td colspan="2"><strong>' + subs.spawnReport + layer.tags.name + '</strong> <em style="font-size:xx-small">' + subs.at + ' ' + layer.tags.centerLat.toFixed(4) + ', ' + layer.tags.centerLon.toFixed(4) + '</em></td></tr>');
            } else {
              $('#spawnReportTable > tbody:last-child').append('<tr><td colspan="2"><strong>' + subs.spawnReport + subs.unnamed + '</strong> ' + subs.at + ' <em style="font-size:xx-small">' + layer.tags.centerLat.toFixed(4) + ', ' + layer.tags.centerLon.toFixed(4) + '</em></td></tr>');
            }
            result.spawns.forEach(function(item) {
              $('#spawnReportTable > tbody:last-child').append('<tr><td>' + pokemon[item.pokemon_id-1] + '</td><td>' + item.count + '</td></tr>');
            });
  } else {
            if (typeof layer.tags.name !== 'undefined') {
              $('#spawnReportTableMissed > tbody:last-child').append('<tr><td colspan="2"><em style="font-size:xx-small"><strong>' + layer.tags.name + '</strong> ' + subs.at + ' ' + layer.tags.centerLat.toFixed(4) + ', ' + layer.tags.centerLon.toFixed(4) + subs.skipped + '</em></td></tr>');
            } else {
              $('#spawnReportTableMissed > tbody:last-child').append('<tr><td colspan="2"><em style="font-size:xx-small"><strong>' + subs.unnamed + '</strong> ' + subs.at + ' ' + layer.tags.centerLat.toFixed(4) + ', ' + layer.tags.centerLon.toFixed(4) + subs.skipped + '</em></td></tr>');
            }
  }
}
$(document).on("click", ".exportLayer", function() {
  $(document.getElementById('copyPolygonOutput')).text(subs.copyClipboard);
  let id = $(this).attr('data-layer-id');
  let layer;
  let container = $(this).attr('data-layer-container');
  switch (container) {
    case 'editableLayer':
      layer = editableLayer.getLayer(parseInt(id));
      break;
    case 'nestLayer':
      layer = nestLayer.getLayer(parseInt(id));
      break;
    case 'admLayer':
      layer = admLayer.getLayer(parseInt(id));
      break;
  }
  // geojson
  let tempLayer = layer.toGeoJSON();
  tempLayer.id = layer.tags.osmid;
  tempLayer.properties.name = layer.tags.name;
  tempLayer.properties.area_center_point = {
    "type": "Point",
    "coordinates": [
      layer._bounds._northEast.lng-((layer._bounds._northEast.lng-layer._bounds._southWest.lng)/2),
      layer._bounds._northEast.lat-((layer._bounds._northEast.lat-layer._bounds._southWest.lat)/2)
    ]
  }
  let polyjson = JSON.stringify(tempLayer);
  $('#exportPolygonDataGeoJson').val(polyjson);
  // simple coords
  let polycoords = '';
  turf.flip(layer.toGeoJSON()).geometry.coordinates[0].forEach(function(item) {
    polycoords += item[0] + ',' + item[1] + "\n";
  });
  $('#exportPolygonDataCoordsList').val(polycoords);
  // poracle
  let po_start = '  {\n    "name": "polygon",\n    "color": "#6CB1E1",\n    "id": 0,\n    "path": [\n';
  let po_end = '    ]\n  }';
  let po_coords = '';
  turf.flip(layer.toGeoJSON()).geometry.coordinates[0].forEach(function(item) {
    po_coords += '      [\n        ' + item[0] + ',\n        ' + item[1] + '\n      ],\n';
  });
  po_coords = po_coords.slice(0, -2) + '\n';
  let poracle = po_start + po_coords + po_end;
  $('#exportPolygonDataPoracle').val(poracle);
  $('#exportPolygonDataGeoJson').hide();
  $('#exportPolygonDataPoracle').hide();
  copyOutput = 'exportPolygonDataCoordsList'
  $('#modalExportPolygon').modal('show');
});
$(document).on("click", ".exportPOIs", function() {
  let id = $(this).attr('data-layer-id');
  let layer = subsLayer.getLayer(parseInt(id));
  let poicoords = 'Lat, Lon: ' + layer._latlng.lat + ', ' + layer._latlng.lng;
  alert(subs.exportPOILabel + '\n' + poicoords)
});
$(document).on("click", ".exportPoints", function() {
  let id = $(this).attr('data-layer-id');
  let layer;
  let container = $(this).attr('data-layer-container');
  switch (container) {
    case 'editableLayer':
      layer = editableLayer.getLayer(parseInt(id));
      break;
    case 'nestLayer':
      layer = nestLayer.getLayer(parseInt(id));
      break;
    case 'admLayer':
      layer = admLayer.getLayer(parseInt(id));
      break;
  }
  let poly = layer.toGeoJSON();
  let line = turf.polygonToLine(poly);
  let gymcoords = '';
  let stopcoords = '';
  let spawncoords = '';
  if (settings.showGyms == true) {
    gyms.forEach(function(item) {
      point = turf.point([item.lng, item.lat]);
      if (turf.inside(point, poly)) {
        gymcoords += item.lat + ',' + item.lng + "\n";
      }
    });
  }
  if (settings.showPokestops == true) {
    pokestops.forEach(function(item) {
      point = turf.point([item.lng, item.lat]);
      if (turf.inside(point, poly)) {
        stopcoords += item.lat + ',' + item.lng + "\n";
      }
    });
  }
  if (settings.showSpawnpoints == true) {
    spawnpoints.forEach(function(item) {
      point = turf.point([item.lng, item.lat]);
      if (turf.inside(point, poly)) {
        spawncoords += item.lat + ',' + item.lng + "\n";
      }
    });
  }
  $('#exportPolygonPointsGyms').val('');
  $('#exportPolygonPointsPokestops').val('');
  $('#exportPolygonPointsSpawnpoints').val('');
  $('#exportPolygonPointsGyms').val(gymcoords);
  $('#exportPolygonPointsPokestops').val(stopcoords);
  $('#exportPolygonPointsSpawnpoints').val(spawncoords);
  $('#modalExportPolygonPoints').modal('show');
});
$(document).on("click", ".countPoints", function() {
  let id = $(this).attr('data-layer-id');
  let layer;
  let container = $(this).attr('data-layer-container');
  switch (container) {
    case 'editableLayer':
      layer = editableLayer.getLayer(parseInt(id));
      break;
    case 'nestLayer':
      layer = nestLayer.getLayer(parseInt(id));
      break;
    case 'admLayer':
      layer = admLayer.getLayer(parseInt(id));
      break;
  }
  let count = 0;
  let gymCount = 0;
  let stopCount = 0;
  let spawnpointCount = 0;
  let poly = layer.toGeoJSON();
  let line = turf.polygonToLine(poly);
  prepareData(layer._bounds);
  if (settings.showGyms == true) {
    gyms.forEach(function(item) {
      point = turf.point([item.lng, item.lat]);
      if (turf.inside(point, poly)) {
        count++;
        gymCount++;
      }
    });
  }
  if (settings.showPokestops == true) {
    pokestops.forEach(function(item) {
      point = turf.point([item.lng, item.lat]);
      if (turf.inside(point, poly)) {
        count++;
        stopCount++;
      }
    });
  }
  if (settings.showSpawnpoints == true) {
    spawnpoints.forEach(function(item) {
      point = turf.point([item.lng, item.lat]);
      if (turf.inside(point, poly)) {
        count++;
        spawnpointCount++;
      }
    });
  }
  alert(subs.countTotal + count + '\n' + subs.countGyms + gymCount + '\n' + subs.countStops + stopCount + '\n' + subs.countSpawnpoints + spawnpointCount);
});
function cellCount(poly) {
  let points = 0;
  cell = poly.toGeoJSON();
  gyms.forEach(function(item) {
    point = turf.point([item.lng, item.lat]);
    if (turf.inside(point, cell)) {
      points++;
    }
  });
  pokestops.forEach(function(item) {
    point = turf.point([item.lng, item.lat]);
    if (turf.inside(point, cell)) {
      points++;
    }
  });
  return points;
};
function loadSettings() {
  const defaultSettings = {
    showGyms: false,
    showPokestops: false,
    showPokestopsRange: false,
    showSpawnpoints: false,
    showUnknownPois: false,
    hideOldSpawnpoints: false,
    showMissingQuests: false,
    showRoute: false,
    circleSize: 70,
    selectCircleRange: 'circleIV',
    optimizationAttempts: 10,
    nestMigrationDate: lastNestChange(),
    oldSpawnpointsTimestamp: 1569438000,
    spawnReportLimit: 10,
    mapMode: 'RouteGenerator',
    mapCenter: [48.85293727329977, 2.3499734637407377],
    mapZoom: 13,
    cellsLevel0: 13,
    cellsLevel0Check: false,
    cellsLevel0Check: false,
    cellsLevel0Check: false,
    tlLink: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    tlChoice: 'osm',
    language: 'en',
    generateWithS2Cells: false
  }
  Object.keys(settings).forEach(function(key) {
    storedSetting = retrieveSetting(key);
    if (storedSetting !== null) {
      settings[key] = storedSetting;      
    } else {
      settings[key] = defaultSettings[key];
      storeSetting(key)
    }
  });
  settings.nestMigrationDate = (parseInt(settings.nestMigrationDate) < lastNestChange()) ? lastNestChange() : settings.nestMigrationDate;
  settings.showMissingQuests = false;
}
function getLanguage() {
  if (settings.language == null) {
    loadSettings()
  }
  if (settings.language == 'de') {
    subs = deSubs;
    pokemon = dePokemon;
  } else if (settings.language == 'fr') {
    subs = frSubs;
    pokemon = frPokemon;
  } else {
    subs = enSubs;
    pokemon = enPokemon;
  }
}
function storeSetting(key) {
  localStorage.setItem(key, JSON.stringify(settings[key]));
}
function retrieveSetting(key) {
  let value;
  if (localStorage.getItem(key) !== 'undefined') {
    value = JSON.parse(localStorage.getItem(key));
  } else {
    value = null;
  }
  return value;
}
function showS2Cells0(level, style) {
  // Credit goes to the PMSF project
  let bounds = map.getBounds();
  const size = L.CRS.Earth.distance(bounds.getSouthWest(), bounds.getNorthEast()) / 4000 + 1 | 0
  const count = 2 ** level * size >> 11
  function addPoly(cell) {
    const vertices = cell.getCornerLatLngs()
    const poly = L.polygon(vertices, Object.assign({opacity: 1.0, fillOpacity: 0.0}, style))
    if (cell.level === settings.cellsLevel0) {
      viewCellLayer.addLayer(poly)
    }
  }
  // add cells spiraling outward
  let cell = S2.S2Cell.FromLatLng(bounds.getCenter(), level)
  let steps = 1
  let direction = 0
  do {
    for (let i = 0; i < 2; i++) {
      for (let i = 0; i < steps; i++) {
        if (bounds.intersects(cell.getCornerLatLngs())) {
          addPoly(cell)
        }
        cell = cell.getNeighbors()[direction % 4]
      }
      direction++
    }
    steps++
  } while (steps < count)
}
function showS2Cells1(level, style) {
  // Credit goes to the PMSF project
  let bounds = map.getBounds();
  const size = L.CRS.Earth.distance(bounds.getSouthWest(), bounds.getNorthEast()) / 4000 + 1 | 0
  const count = 2 ** level * size >> 11
  if (settings.s2CountPOI != false) {
    prepareData(bounds)
  }
  function addPoly(cell) {
    const vertices = cell.getCornerLatLngs()
    const poly = L.polygon(vertices, Object.assign({opacity: 1.0, fillOpacity: 0.0}, style))
    if (cell.level === settings.cellsLevel1) {
      viewCellLayer.addLayer(poly)
      if (settings.s2CountPOI != false) {
        let poiCount = cellCount(poly).toString();
        let marker = L.circleMarker([vertices[3].lat, vertices[3].lng], { stroke: false, radius: 1, fillOpacity: 0.0 }); 
        marker.bindTooltip(poiCount, {permanent: true, textOnly: true, opacity: 0.8, direction: 'center', offset: [25, -20] })
        viewCellLayer.addLayer(marker);
      }
    }
  }
  // add cells spiraling outward
  let cell = S2.S2Cell.FromLatLng(bounds.getCenter(), level)
  let steps = 1
  let direction = 0
  do {
    for (let i = 0; i < 2; i++) {
      for (let i = 0; i < steps; i++) {
        if (bounds.intersects(cell.getCornerLatLngs())) {
          addPoly(cell)
        }
        cell = cell.getNeighbors()[direction % 4]
      }
      direction++
    }
    steps++
  } while (steps < count)
}
function showS2Cells2(level, style) {
  // Credit goes to the PMSF project
  const bounds = map.getBounds()
  const size = L.CRS.Earth.distance(bounds.getSouthWest(), bounds.getNorthEast()) / 4000 + 1 | 0
  const count = 2 ** level * size >> 11
  function addPoly(cell) {
    const vertices = cell.getCornerLatLngs()
    const poly = L.polygon(vertices, Object.assign({opacity: 1.0, fillOpacity: 0.0}, style))
    if (cell.level === settings.cellsLevel2) {
      viewCellLayer.addLayer(poly)
    }
  }
  // add cells spiraling outward
  let cell = S2.S2Cell.FromLatLng(bounds.getCenter(), level)
  let steps = 1
  let direction = 0
  do {
    for (let i = 0; i < 2; i++) {
      for (let i = 0; i < steps; i++) {
        if (bounds.intersects(cell.getCornerLatLngs())) {
          addPoly(cell)
        }
        cell = cell.getNeighbors()[direction % 4]
      }
      direction++
    }
    steps++
  } while (steps < count)
}
function zoomIn(factor) {
  map.setZoom(factor)
}
function updateS2Overlay() {
  if (map.getZoom() >= 13.5) {
    viewCellLayer.clearLayers()
    if (settings.cellsLevel0Check != false  && settings.cellsLevel0 < 20) {
      showS2Cells0(settings.cellsLevel0, {color: 'Red', weight: 1})
    } else if (settings.cellsLevel0Check != false && settings.cellsLevel0 > 19) {
      viewCellLayer.clearLayers()
      if (map.getZoom() < 17.5){
        zoomIn(17.5)
        console.log('Zoom adapted for L20 cells')
      } else {
        showS2Cells0(settings.cellsLevel0, {color: 'Red', weight: 0.5}) 
      }       
    }
    if (settings.cellsLevel1Check != false && settings.s2CountPOI == false) {
      showS2Cells1(settings.cellsLevel1, {color: 'Blue', weight: 2})
    } else if (settings.cellsLevel1Check != false && settings.s2CountPOI != false) {
      viewCellLayer.clearLayers()
      if (map.getZoom() < 14.5){
        zoomIn(14.5)
        console.log('Zoom adapted for L14 cells with POI Count')
      } else {
        showS2Cells1(settings.cellsLevel1, {color: 'Blue', weight: 2})
      }
    }  
    if (settings.cellsLevel2Check != false) {
      showS2Cells2(settings.cellsLevel2, {color: 'Green', weight: 1})
    }        
    editableLayer.removeFrom(map).addTo(map);
    nestLayer.removeFrom(map).addTo(map);
    circleLayer.removeFrom(map).addTo(map);
    instanceLayer.removeFrom(map).addTo(map);
  } else {
    viewCellLayer.clearLayers()
    if (settings.cellsLevel0Check != false || settings.cellsLevel1Check != false || settings.cellsLevel2Check != false) {
      console.log('View cells are currently hidden, zoom in')
    }
  }
}
$(document).on("click", ".mergePolygons", function() {
  let polygonOptions = {
    clickable: false,
    color: "#111111",
    fill: true,
    fillColor: null,
    fillOpacity: 0.1,
    opacity: 0.5,
    stroke: true,
    weight: 4
  };
  let activeLayer;
  if (editableLayer.getLayers().length != 0) {
    activeLayer = editableLayer;
  } else if (admLayer.getLayers().length != 0) {
    activeLayer = admLayer;
  }
  let base = activeLayer.getLayers()[0].toGeoJSON();
  activeLayer.getLayers().forEach(function(item) {
    base = turf.union(base, item.toGeoJSON());
  });
  activeLayer.clearLayers();
  let layer = L.polygon(turf.flip(base).geometry.coordinates, polygonOptions).addTo(editableLayer);
  layer.tags.merged = true;
});
function makeTextFile (text) {
  let textFile = null;
  let data = new Blob([text], {
    type: 'text/plain'
  });
  if (textFile !== null) {
    window.URL.revokeObjectURL(textFile);
  }
  textFile = window.URL.createObjectURL(data);
  return textFile;
};
$(document).on("click", "#generateNestFile", function () {
  let nests = '';
  let content = '';
  let link = document.getElementById('downloadlink');
  let newid = 0;
  let id;
  // Format switch
  let exportType = $("#modalOutput input[name=exportJsonType]:checked").val()
  console.log('exportType: ', exportType)
  if (exportType == 'json') {
    exportList.eachLayer(function(layer){
      // json
      if (layer.tags.osmid != undefined) {
        id = layer.tags.osmid;
      } else {
        id = newid;
      }
      newid++;
      let start = '\n  {\n    "type": "Feature",\n    "properties": {\n      "name": "' + layer.tags.name + '",\n      "description": null\n    },\n    "geometry": {\n      "type": "Polygon",\n      "coordinates": [[\n';
      let end = '      ]]\n    }\n  },';
      let coords = '';
      turf.flip(layer.toGeoJSON()).geometry.coordinates[0].forEach(function(item) {
        coords += '      [' + item[1] + ',' + item[0] + '],\n'; 
      });
      coords = coords.slice(0, -2) + '\n';
      let json = start + coords + end;

      nests += json;
      
    });
    nests = nests.slice(0, -1);
    content = '{\n  "type": "FeatureCollection",\n  "features":[' + nests + ']\n}';
    link.download = 'nests.json';
  }
  if (exportType == 'pmsf') {
    exportList.eachLayer(function(layer){
      // pmsf
      if (layer.tags.osmid != undefined) {
        id = layer.tags.osmid;
      } else {
        id = newid;
      }
      newid++;
      min_lat = layer._bounds._southWest.lat;
      max_lon = layer._bounds._northEast.lng;
      max_lat = layer._bounds._northEast.lat;
      min_lon = layer._bounds._southWest.lng;
      cen_lat = layer.getCenter().lat;
      cen_lon = layer.getCenter().lng;
      let start = '\n        {\n            "geometry": {\n                "type": "Polygon",\n                "coordinates": [\n                    [\n';
      let end = '                    ]\n                ]\n            },\n            "type": "Feature",\n            "id": ' + id + ',\n            "properties": {\n                "fill-opacity": 0.3,\n                "min_lat": ' + min_lat + ',\n                "max_lon": ' + max_lon + ',\n                "stroke": "#0c6602",\n                "stroke-width": 1.0,\n                "fill": "#0c6602",\n                "name": "' + layer.tags.name + '",\n                "stroke-opacity": 1.0,\n                "max_lat": ' + max_lat + ',\n                "min_lon": ' + min_lon + ',\n                "area_center_point": {\n                    "type": "Point",\n                    "coordinates": [\n                        ' + cen_lon + ',\n                        ' + cen_lat + '\n                    ]\n                }\n            }\n        },';
      let coords = '';
      turf.flip(layer.toGeoJSON()).geometry.coordinates[0].forEach(function(item) {
        coords += '                        [\n                            ' + item[1] + ',\n                            ' + item[0] + '\n                        ],\n'; 
      });
      coords = coords.slice(0, -2) + '\n';
      let pmsf = start + coords + end;
      nests += pmsf;
    });
    nests = nests.slice(0, -1);
    content = '{\n    "type": "FeatureCollection",\n    "features": [' + nests + '\n    ]\n}';
    link.download = 'nest.json';
  }
  if (exportType == 'poracle') {
    exportList.eachLayer(function(layer){
      // poracle
      if (layer.tags.osmid != undefined) {
        id = layer.tags.osmid;
      } else {
        id = newid;
      }
      newid++;
      let start = '\n  {\n    "name": "' + layer.tags.name + '",\n    "color": "#6CB1E1",\n    "id": ' + id + ',\n    "path": [\n';
      let end = '    ]\n  },';
      let coords = '';
      turf.flip(layer.toGeoJSON()).geometry.coordinates[0].forEach(function(item) {
        coords += '      [\n        ' + item[0] + ',\n        ' + item[1] + '\n      ],\n'; 
      });
      coords = coords.slice(0, -2) + '\n';
      let poracle = start + coords + end;
      nests += poracle;
    });
    nests = nests.slice(0, -1);
    content = '[' + nests + '\n]';
    link.download = 'geofence.json';
  }
  if (exportType == 'simple') {
    exportList.eachLayer(function(layer){
      // simple coordlist
      let start = '[' + layer.tags.name + ']';
      let coords = '';
      layer.toGeoJSON().geometry.coordinates[0].forEach(function(item) {
        coords += '\n' + item[1] + ',' + item[0];
      });
      let simple = start + coords + '\n';
      nests += simple;
    });
    content = nests;
    link.download = 'coords.txt';
  }
  // Output
  link.href = makeTextFile(content);
  document.getElementById('downloadlink').click();
});
function newMSInstances() {
  mySelect = [];
  for (let i = 0; i < instances.length; i++) {
    if (instances[i].length > 0) {
      mySelect.push(instances[i].name);
    }
  }
  if (mySelect != '') {
    $('.multi_0').multi_select({
      data: mySelect,
      selectColor: "blue",
      selectSize: "small",
      selectText: subs.selectInstances
    });
  } else {
    $('.multi_0').multi_select({
      data: '',
      selectColor: "blue",
      selectSize: "small",
      selectText: subs.selectInstances
    });
  }
}
function newMSQuests() {
  const data = {
    'get_instance_names': true,
  };
  myQuestSelect = [];
  const json = JSON.stringify(data);
  $.ajax({
    url: this.href,
    type: 'POST',
    async: false,
    dataType: 'json',
    data: {'data': json},
    success: function (result) {
      result.forEach(function(item) {
        if (item.type == 'auto_quest') {
          myQuestSelect.push(item.name)
        }
      });
    }
  });
  $('.multi_1').multi_select({
    data: myQuestSelect,
    selectColor: "blue",
    selectSize: "small",
    selectText: subs.selectInstances
  });
}
</script>

</head>
  <body>
    <div id="map"></div>

    <div class="modal" id="modalSettings" tabindex="-1" role="dialog" style="min-width: 400px;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.settings);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><script type="text/javascript">document.write(subs.routeOptAtt);</script></span>
              </div>
              <input id="optimizationAttempts" name="optimizationAttempts" type="text" class="form-control" aria-label="Optimization attempts">
              <div class="input-group-append">
                <span class="input-group-text"><script type="text/javascript">document.write(subs.tries);</script></span>
              </div>
            </div>

            <div class="input-group mb">              
              <div class="input-group">
                <label class="form-check-label"><script type="text/javascript">document.write("S2 Cells");</script></label>
              </div>
            </div>
            
            <div class="input-group mb-3">
              <div class="input-group-text" style="background-color: white; border-width: 0px;">
                <span class="form-check-label"><script type="text/javascript">document.write(subs.s2cells1);</script>
                <input type="checkbox" name="cellsLevel1Check" id="cellsLevel1Check" style="margin-left: 10px; margin-right: 25px;">
                <script type="text/javascript">document.write(subs.s2CountPOI);</script>
                <input type="checkbox" name="s2CountPOI" id="s2CountPOI" style="margin-left: 10px;"></span>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="input-group-text" style="background-color: white; border-width: 0px;">
                <span class="form-check-label"><script type="text/javascript">document.write(subs.s2cells2);</script></span>
                <input type="checkbox" name="cellsLevel2Check" id="cellsLevel2Check" style="margin-left: 10px;">
              </div>
              <div class="input-group-text" style="background-color: white; border-width: 0px; margin-left: 7px">
                <span class="form-check-label"><script type="text/javascript">document.write(subs.s2cells0);</script></span>
                <input id="cellsLevel0" name="cellsLevel0" type="text" aria-label="cells Level 0" style="margin-left: 10px; width: 60px;">
                <input type="checkbox" name="cellsLevel0Check" id="cellsLevel0Check" style="margin-left: 10px;">
              </div>
            </div>
            <div class="input-group" style="margin-bottom: 15px; margin-top: 10px;">
              <div>
                <input type="checkbox" name="generateWithS2Cells" id="generateWithS2Cells" style="margin-left: 10px; margin-right: 10px">
              </div>
              <div>
                <label><script type="text/javascript">document.write(subs.generateWithS2Cells);</script></label>
              </div>
            </div>
            <div class="input-group mb">              
              <div class="input-group">
                <label class="form-check-label"><script type="text/javascript">document.write(subs.circleRadius);</script></label>
              </div>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-text" style="margin-left: 10px; background-color: white; border-width: 0px;">
                <input class="form-check-input" type="radio" name="selectCircleRange" id="circleIV" value="circleIV">
                <label class="form-check-label" for="circleIV"><script type="text/javascript">document.write('IV (70m)');</script></label>
                </div>
              <div class="input-group-text" style="margin-left: 10px; background-color: white; border-width: 0px;">
                <input class="form-check-input" type="radio" name="selectCircleRange" id="circleRaid" value="circleRaid">
                <label class="form-check-label" for="circleRaid"><script type="text/javascript">document.write('Raid (auto)');</script></label>
              </div>
              <div class="input-group-text" style="margin-left: 10px; background-color: white; border-width: 0px;">
                <input class="form-check-input" type="radio" name="selectCircleRange" id="circle1gb" value="circle1gb">
                <label class="form-check-label" for="circle1gb"><script type="text/javascript">document.write(subs.oldDevices + ' (auto)');</script></label>
              </div>
              <div class="input-group-text" style="margin-left: 10px; background-color: white; border-width: 0px;">
                <input class="form-check-input" type="radio" name="selectCircleRange" id="circleOwn" value="circleOwn">
                <label class="form-check-label" for="circleOwn"><script type="text/javascript">document.write(subs.other);</script></label>
              </div>
              <input id="circleSize" name="circleSize" type="text" size="3" class="form-control" aria-label="Circle radius (in meters)" placeholder="500">
              <div class="input-group-append">
                <span class="input-group-text"><script type="text/javascript">document.write('m');</script></span>
              </div>
            </div>

            <div class="input-group mb-3 date" id="nestMigrationDate" data-target-input="nearest">
              <div class="input-group-prepend">
                <span class="input-group-text"><script type="text/javascript">document.write(subs.lastNestMigration);</script></span>
              </div>
              <input type="text" class="form-control datetimepicker-input" data-target="#nestMigrationDate"/>
              <div class="input-group-append" data-target="#nestMigrationDate" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><script type="text/javascript">document.write(subs.spawnReportLimit);</script></span>
              </div>
              <input id="spawnReportLimit" name="spawnReportLimit" type="text" class="form-control" aria-label="Spawn report limit">
              <div class="input-group-append">
                <span class="input-group-text"><script type="text/javascript">document.write(subs.pokemon);</script></span>
              </div>
            </div>

            <label><script type="text/javascript">document.write(subs.oldSpawnpointsTitle);</script></label>
            <div class="input-group mb-3 date" id="oldSpawnpointsTimestamp" data-target-input="nearest">
              <input type="text" class="form-control datetimepicker-input" data-target="#oldSpawnpointsTimestamp"/>
              <div class="input-group-append" data-target="#oldSpawnpointsTimestamp" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
              </div>
            </div>

            <div class="form-group">
              <label for="language"><script type="text/javascript">document.write(subs.selectLanguage);</script></label>
              <select class="form-control" id="language">
                <option value="en">English</option>
                <option value="de">Deutsch</option>
                <option value="fr">Français</option>
              </select>
            </div>

            <div class="form-group">
              <label for="tlChoice"><script type="text/javascript">document.write(subs.chooseTileset);</script></label>
              <select class="form-control" id="tlChoice">
                <option value="osm">Standard (OSM)</option>
                <option value="carto">Lite</option>
                <option value="sat">Satellite</option>
                <option value="own"><script type="text/javascript">document.write(subs.ownTileset);</script></option>
                <option value="topo">Topographic</option>
                <option value="dark">Dark</option>
              </select>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" id="saveSettings" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modalOutput" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.output);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="input-group mb-3">
              <div class="multi_0" id="multiInstances" style="min-width: 300px;"></div>
            </div>
            <label for="mapMode"><script type="text/javascript">document.write(subs.generatedRoute)</script></label>
            <div class="input-group mb-3">
              <textarea id="outputCircles" style="height:120px;" class="form-control" aria-label="Route output"></textarea>
            </div>
            <dl class="row">
              <dt class="col-sm-7"><script type="text/javascript">document.write(subs.countCircles)</script></dt>
              <dd class="col-sm-3"><output id="outputCirclesCount" aria-label="Circle count output"></output></dd>
              <dt class="col-sm-7"><script type="text/javascript">document.write(subs.outputAvgPt)</script></dt>
              <dd class="col-sm-3"><output id="outputAvgPt" aria-label="Average ppc output"></output></dd>
            </dl>
            <div class="btn-toolbar" style="margin-bottom: 20px;">
              <div class="btn-group mr-2" role="group" aria-label="">
                <button id="getOutput" class="btn btn-primary float-left" type="button"><script type="text/javascript">document.write(subs.getOutput);</script></button>
              </div>
              <div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="exportCoordsType" id="exportCoordsTypeUnsorted" value="unsorted" checked>
                  <label class="form-check-label" for="exportCoordsTypeUnsorted"><script type="text/javascript">document.write(subs.coordTypeUnsorted);</script></label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="exportCoordsType" id="exportCoordsTypeSorted" value="sorted">
                  <label class="form-check-label" for="exportCoordsTypeSorted"><script type="text/javascript">document.write(subs.coordTypeSorted);</script></label>
                </div>
              </div>
              <div class="btn-group" role="group" aria-label=""  style='margin-left: 20px;'>
                <button id="copyCircleOutput" class="btn btn-secondary float-right" type="button"><script type="text/javascript">document.write(subs.copyClipboard);</script></button>
              </div>
            </div>
            <div class="btn-toolbar" style="margin-bottom: 20px;">
              <div class="btn-group" role="group" aria-label="">
                <button id="getCirclesCount" class="btn btn-primary float-right" type="button"><script type="text/javascript">document.write(subs.countPoints);</script></button>
              </div>
            </div>
              <dl class="row" style="margin-bottom: 5px;">
                <dt class="col-sm-7"><script type="text/javascript">document.write(subs.exportListCount)</script></dt>
                <dd class="col-sm-3"><output id="exportListCount" aria-label="Exportlist count"></output></dd>
              </dl>
            <div class="btn-toolbar">
              <div class="btn-group" role="group" aria-label="">
                <button id="generateNestFile" class="btn btn-primary float-left" type="button" style='margin-right: 10px;'><script type="text/javascript">document.write(subs.saveFile);</script></button>
              </div>
              <div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="exportJsonType" id="exportJsonTypeClassic" value="json" checked>
                  <label class="form-check-label" for="exportJsonTypeClassic">JSON</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="exportJsonType" id="exportJsonTypePMSF" value="pmsf">
                  <label class="form-check-label" for="exportJsonTypePMSF">PMSF/RDM</label>
                </div>
              </div>
              <div style="margin-left: 15px;">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="exportJsonType" id="exportJsonTypePoracle" value="poracle">
                  <label class="form-check-label" for="exportJsonTypePoracle">Poracle</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="exportJsonType" id="exportJsonTypeSimple" value="simple">
                  <label class="form-check-label" for="exportJsonTypeSimple">Simple</label>
                </div>
                <div class="form-check">
                  <a download="nest.json" id="downloadlink" style="display: none">Download</a>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modalNests" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.nestOptions);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="btn-toolbar" style="margin-bottom: 20px;">
              <div class="btn-group" role="group" aria-label="">
                <button id="getAllNests" class="btn btn-primary float-left" type="button" style='margin-right: 10px;'><script type="text/javascript">document.write(subs.getAllNests);</script></button>
              </div>
            </div>

            <div class="input-group mb">              
              <div class="input-group">
                <h6 class="modal-title" style="margin-bottom: 5px;"><script type="text/javascript">document.write(subs.osmOptions);</script></h6>
              </div>
            </div>
            <div>
              <div class="form-check form-check-inline" style="width: 50%;">
                <input class="form-check-input" type="checkbox" id="osmOption1" value="osmOption1" checked>
                <label class="form-check-label" for="osmOption1"><script type="text/javascript">document.write(subs.osmPark);</script></label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="osmOption2" value="osmOption2">
                <label class="form-check-label" for="osmOption4"><script type="text/javascript">document.write(subs.osmMeadow);</script></label>
              </div>
            </div>
            <div>
              <div class="form-check form-check-inline" style="width: 50%;">
                <input class="form-check-input" type="checkbox" id="osmOption3" value="osmOption3" checked>
                <label class="form-check-label" for="osmOption3"><script type="text/javascript">document.write(subs.osmRecGround);</script></label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="osmOption4" value="osmOption4">
                <label class="form-check-label" for="osmOption6"><script type="text/javascript">document.write(subs.osmGrass);</script></label>
              </div>
            </div>
            <div>
              <div class="form-check form-check-inline" style="width: 50%;">
                <input class="form-check-input" type="checkbox" id="osmOption5" value="osmOption5">
                <label class="form-check-label" for="osmOption5"><script type="text/javascript">document.write(subs.osmPitch);</script></label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="osmOption6" value="osmOption6">
                <label class="form-check-label" for="osmOption6"><script type="text/javascript">document.write(subs.osmGolf);</script></label>
              </div>
            </div>
            <div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="osmOption7" value="osmOption7">
                <label class="form-check-label" for="osmOption7"><script type="text/javascript">document.write(subs.osmPlayground);</script></label>
              </div>
            </div>
            <div class="btn-toolbar" style="margin-top: 20px;">
              <div class="btn-group" role="group" aria-label="">
                <button id="importNestsOSM" class="btn btn-primary float-left" type="button" style='margin-right: 10px;'><script type="text/javascript">document.write(subs.osmImport);</script></button>
              </div>
            </div>

            <div class="input-group" style="margin-top: 20px; margin-bottom: 10px;">
              <h6 class="modal-title"><script type="text/javascript">document.write(subs.manualdbHint)</script></h6>
            </div>
            <div class="btn-toolbar" style="margin-bottom: 20px;">
              <div class="btn-group" role="group" aria-label="">
                <button id="importNests" class="btn btn-primary float-left" type="button" style='margin-right: 10px;'><script type="text/javascript">document.write(subs.importFromDB);</script></button>
              </div>
            </div>
            <div class="btn-toolbar" style="margin-bottom: 20px;">
              <div class="btn-group" role="group" aria-label="">
                <button id="updateDb" class="btn btn-primary float-left updateButton" type="button" style='margin-right: 10px;'><script type="text/javascript">document.write(subs.writeAllToDB);</script></button>
              </div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modalQuestInstances" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.questCheck);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="input-group mb-3">
              <div class="multi_1" id="multiQuest" style="min-width: 300px;"></div>
            </div>
          </div>
          <div class="input-group" style="margin-bottom: 15px; margin-top: 10px;">
            <div>
              <input type="checkbox" name="instanceBorders" id="instanceBorders" style="margin-left: 20px; margin-right: 10px">
            </div>
            <div>
              <label><script type="text/javascript">document.write(subs.showBorders);</script></label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="showMissingQuests"><script type="text/javascript">document.write(subs.questCheckButton);</script></button>
            <button type="button" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modalImportInstance" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.importInstance);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <label for="importInstanceName"><script type="text/javascript">document.write(subs.selectInstance);</script></label>
            <div class="input-group mb-3">
              <select name="importInstanceName" id="importInstanceName" class="form-control" aria-label="Select an instance to import">
              </select>
            </div>
            <div class="input-group mb-3">
              <div>
                <input type="checkbox" name="instanceRadiusCheck" id="instanceRadiusCheck" style="margin-right: 15px; vertical-align: bottom;">
              </div>
              <div class="input-group-prepend">
                <span class="input-group-text"><script type="text/javascript">document.write(subs.ownRadius);</script></span>
              </div>
              <input id="ownRadius" name="ownRadius" type="text" aria-label="Own radius for instance import" style="padding-left: 10px; width: 80px;">
            </div>

            <div>
              <label for="importCircleData"><script type="text/javascript">document.write(subs.importCirclesHl);</script></label>
              <div class="input-group mb">
                <textarea name="importCircleData" id="importCircleData" style="height:120px;" class="form-control" aria-label="Circle data"></textarea>
              </div>
            </div>

            <div class="input-group" style="margin-bottom: 15px; margin-top: 10px;">
              <div>
                <label><script type="text/javascript">document.write(subs.instanceMode);</script></label>
              </div>
              <div>
                  <input type="checkbox" name="instanceMode" id="instanceMode" style="margin-left: 15px;">
              </div>
            </div>

            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><script type="text/javascript">document.write(subs.instanceColor);</script></span>
                <input type="text" value="3388ff" name="instanceColor" class="pick-a-color form-control">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="importInstance" class="btn btn-primary"><script type="text/javascript">document.write(subs.importInstance);</script></button>
            <button type="button" id="importCircles" class="btn btn-primary"><script type="text/javascript">document.write(subs.importCirclesBtn);</script></button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal" id="modalImportPolygon" tabindex="-1" role="dialog">
      <form id="importPolygonForm">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><script type="text/javascript">document.write(subs.importPolygon);</script></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <label for="importPolygonDataType"><script type="text/javascript">document.write(subs.polygonDataType);</script></label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="importPolygonDataType" id="importPolygonDataTypeCoordList" value="importPolygonDataTypeCoordList" checked>
                <label class="form-check-label" for="importPolygonDataTypeCoordList"><script type="text/javascript">document.write(subs.coordinateList);</script></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="importPolygonDataType" id="importPolygonDataTypeGeoJson" value="importPolygonDataTypeGeoJson">
                <label class="form-check-label" for="importPolygonDataTypeGeoJson"><script type="text/javascript">document.write(subs.geoJson);</script></label>
              </div>
              <label for="importPolygonData"><script type="text/javascript">document.write(subs.polygonData);</script></label>
              <div class="input-group mb">
                <textarea name="importPolygonData" id="importPolygonData" style="height:200px;" class="form-control" aria-label="Polygon data"></textarea>
              </div>
              <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><script type="text/javascript">document.write(subs.polygonColor);</script></span>
                <input type="text" value="3388ff" name="polygonColor" class="pick-a-color form-control">
              </div>
            </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="savePolygon" class="btn btn-primary"><script type="text/javascript">document.write(subs.import);</script></button>
              <button type="button" id="saveNestPolygon" class="btn btn-secondary"><script type="text/javascript">document.write(subs.importNest);</script></button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="modal" id="modalImportSubmissions" tabindex="-1" role="dialog">
      <form id="importSubmissionsForm">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><script type="text/javascript">document.write(subs.importSubmissions);</script></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <label for="importSubmissionsData"><script type="text/javascript">document.write(subs.poiData);</script></label>
              <div class="input-group mb">
                <textarea name="importSubmissionsData" id="importSubmissionsData" style="height:250px;" class="form-control" aria-label="Submissions data"></textarea>
              </div>
            </div>
            <div class="modal-body">
              <label for="csvOpener"><script type="text/javascript">document.write(subs.csvOpener);</script></label>
              <input id="csvOpener" type='file' accept='text/csv' onchange='openFile(event)'>
                <script>
                  let openFile = function(event) {
                    let input = event.target;
                    let reader = new FileReader();
                    reader.onload = function(){
                      csvImport = reader.result;
                    };
                    reader.readAsText(input.files[0]);
                  };
                </script>
            </div>
            <div class="modal-body">       
              <label for="submissionRangeCheck"><script type="text/javascript">document.write(subs.submissionRangeCheck);</script></label>
              <input type="checkbox" name="submissionRangeCheck" id="submissionRangeCheck" style="margin-left: 15px; vertical-align: middle;">
            </div>
            <div class="modal-footer">
              <button type="button" id="importSubmissions" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.import);</script></button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="modal" id="modalExportPolygon" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.exportPolygon);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          <label for="exportPolygonDataType"><script type="text/javascript">document.write(subs.polygonDataType);</script></label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="exportPolygonDataType" id="exportPolygonDataTypeCoordsList" value="exportPolygonDataTypeCoordsList" checked>
                <label class="form-check-label" for="exportPolygonDataTypeCoordsList"><script type="text/javascript">document.write(subs.coordinateList);</script></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="exportPolygonDataType" id="exportPolygonDataTypeGeoJson" value="exportPolygonDataTypeGeoJson">
                <label class="form-check-label" for="exportPolygonDataTypeGeoJson"><script type="text/javascript">document.write(subs.geoJson);</script></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="exportPolygonDataType" id="exportPolygonDataTypePoracle" value="exportPolygonDataTypePoracle">
                <label class="form-check-label" for="exportPolygonDataTypePoracle"><script type="text/javascript">document.write(subs.poracle);</script></label>
              </div>
            <label for="exportPolygonData"><script type="text/javascript">document.write(subs.polygonData);</script></label>
            <div class="input-group mb">
              <textarea name="exportPolygonDataGeoJson" id="exportPolygonDataGeoJson" style="height:400px;" class="form-control" aria-label="Polygon data"></textarea>
            </div>
            <div class="input-group mb">
              <textarea name="exportPolygonDataCoords" id="exportPolygonDataCoordsList" style="height:400px;" class="form-control" aria-label="Polygon data"></textarea>
            </div>
            <div class="input-group mb">
              <textarea name="exportPolygonDataPoracle" id="exportPolygonDataPoracle" style="height:400px;" class="form-control" aria-label="Polygon data"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button id="copyPolygonOutput" class="btn btn-secondary float-left" type="button"><script type="text/javascript">document.write(subs.copyClipboard);</script></button>
            <button type="button" id="exportPolygonClose" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modalExportPolygonPoints" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.exportPolygonPoints);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <label for="exportPolygonData"><script type="text/javascript">document.write(subs.gyms);</script></label>
            <div class="input-group mb">
              <textarea name="exportPolygonPointsGyms" id="exportPolygonPointsGyms" style="height:200px;" class="form-control" aria-label="Gym data"></textarea>
            </div>
            <label for="exportPolygonData"><script type="text/javascript">document.write(subs.pokestops);</script></label>
            <div class="input-group mb">
              <textarea name="exportPolygonPointsPokestops" id="exportPolygonPointsPokestops" style="height:200px;" class="form-control" aria-label="Pokestop data"></textarea>
            </div>
            <label for="exportPolygonData"><script type="text/javascript">document.write(subs.spawnpoints);</script></label>
            <div class="input-group mb">
              <textarea name="exportPolygonPointsSpawnpoints" id="exportPolygonPointsSpawnpoints" style="height:200px;" class="form-control" aria-label="Spawnpoint data"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="exportPolygonPointsClose" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modalOptimize" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.optimize);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="optimizeTypePOI" id="optimizeForGyms" checked>
                <label class="form-check-label" for="optimizeForGyms"><script type="text/javascript">document.write(subs.optimizeGyms);</script></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="optimizeTypePOI" id="optimizeForPokestops">
                <label class="form-check-label" for="optimizeForPokestops"><script type="text/javascript">document.write(subs.optimizePokestops);</script></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="optimizeTypePOI" id="optimizeForSpawnpoints">
                <label class="form-check-label" for="optimizeForSpawnpoints"><script type="text/javascript">document.write(subs.optimizeSpawnpoints);</script></label>
              </div>
              <div class="form-check" style="margin-bottom: 10px;">
                <input class="form-check-input" type="radio" name="optimizeTypePOI" id="optimizeForUnknownSpawnpoints">
                <label class="form-check-label" for="optimizeForUnknownSpawnpoints"><script type="text/javascript">document.write(subs.optimizeUnknownSpawnpoints);</script></label>
              </div>
            </div>
            <hr>
            <div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="optimizeTypeLayer" id="optimizePolygons" checked>
                <label class="form-check-label" for="optimizePolygons"><script type="text/javascript">document.write(subs.optimizePiP);</script></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="optimizeTypeLayer" id="optimizeNests">
                <label class="form-check-label" for="optimizeNests"><script type="text/javascript">document.write(subs.optimizePiNP);</script></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="optimizeTypeLayer" id="optimizeCircles">
                <label class="form-check-label" for="optimizeCircles"><script type="text/javascript">document.write(subs.optimizePiC);</script></label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="getOptimizedRoute" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.getOptimization);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modalAdBounds" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><script type="text/javascript">document.write(subs.adBoundsHeader);</script></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div>
              <table>
                <tr>
                  <td>
                    <label class="form-check-label" style="margin-right: 40px;"><script type="text/javascript">document.write(subs.adBounds1);</script></label>
                  </td>
                  <td>
                    <input class="form-check-input" type="radio" name="selectAdBoundsLv" id="adBounds1">
                    <label class="form-check-label" for="adBounds1">Lv. 6</label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <label class="form-check-label" style="margin-right: 40px;"><script type="text/javascript">document.write(subs.adBounds2);</script></label>
                  </td>
                  <td>
                    <input class="form-check-input" type="radio" name="selectAdBoundsLv" id="adBounds2_1">
                    <label class="form-check-label" for="adBounds2_1" style="margin-right: 30px;">Lv. 6</label>
                    <input class="form-check-input" type="radio" name="selectAdBoundsLv" id="adBounds2_2" checked>
                    <label class="form-check-label" for="adBounds2_2">Lv. 8</label>
                  </td>
                </tr>
                <tr>
                  <td>
                    <label class="form-check-label" style="margin-right: 40px;"><script type="text/javascript">document.write(subs.adBounds3);</script></label> 
                  </td>
                  <td>
                    <input class="form-check-input" type="radio" name="selectAdBoundsLv" id="adBounds3_1">
                    <label class="form-check-label" for="adBounds3_1" style="margin-right: 30px;">Lv. 9</label>
                    <input class="form-check-input" type="radio" name="selectAdBoundsLv" id="adBounds3_2">
                    <label class="form-check-label" for="adBounds3_2" style="margin-right: 30px;">Lv. 10</label>
                    <input class="form-check-input" type="radio" name="selectAdBoundsLv" id="adBounds3_3">
                    <label class="form-check-label" for="adBounds3_3">Lv. 11</label>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="getAdBounds" class="btn btn-primary" data-dismiss="modal"><script type="text/javascript">document.write(subs.getAdBounds);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modalSpawnReport" tabindex="-1" role="dialog" style="overflow: auto;">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-sm" id="spawnReportTable">
              <thead>
                <tr>
                  <th scope="col"><script type="text/javascript">document.write(subs.pokemon);</script>:</th>
                  <th scope="col"><script type="text/javascript">document.write(subs.count);</script></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <table class="table table-sm" id="spawnReportTableMissed">
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary writeNest"><script type="text/javascript">document.write(subs.writeToDB);</script></button>
            <button type="button" class="btn btn-secondary closeSr"  data-dismiss="modal"><script type="text/javascript">document.write(subs.close);</script></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal modal-loader" id="modalLoading" data-backdrop="static" data-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-sm">
        <div class="modal-content" style="width: 48px">
          <span class="fa fa-spinner fa-spin fa-3x"></span>
        </div>
      </div>
    </div>
  </body>
</html>

<?php
}
function initDB($DB_HOST, $DB_USER, $DB_PSWD, $DB_NAME, $DB_PORT) {
  $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;port=$DB_PORT;charset=utf8mb4";
  $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => true,
  ];
  $pdo = new PDO($dsn, $DB_USER, $DB_PSWD, $options);
  return $pdo;
}
function initMDB($MDB_HOST, $MDB_USER, $MDB_PSWD, $MDB_NAME, $MDB_PORT) {
  $dsn = "mysql:host=$MDB_HOST;dbname=$MDB_NAME;port=$MDB_PORT;charset=utf8mb4";
  $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => true,
  ];
  $mpdo = new PDO($dsn, $MDB_USER, $MDB_PSWD, $options);
  return $mpdo;
}
function map_helper_init() {
  global $db;
  $db = initDB(DB_HOST, DB_USER, DB_PSWD, DB_NAME, DB_PORT);
  if (MDB_ACTIVE === true) {
    global $mdb;
    $mdb = initMDB(MDB_HOST, MDB_USER, MDB_PSWD, MDB_NAME, MDB_PORT);
  }
  $args = json_decode($_POST['data']);
  if (isset($args->get_spawndata)) {
  if ($args->get_spawndata === true) { getSpawnData($args); }
  }
  if (isset($args->get_data)) {
  if ($args->get_data === true) { getData($args); }
  }
  if (isset($args->get_optimization)) {
  if ($args->get_optimization === true) { getOptimization($args); }
  }
  if (isset($args->get_instance_data)) {
  if ($args->get_instance_data === true) { getInstanceData($args); }
  }
  if (isset($args->get_instance_names)) {
  if ($args->get_instance_names === true) { getInstanceNames($args); }
  }
  if (isset($args->set_nest_data)) {
  if ($args->set_nest_data === true) { setNestData($args); }
  }
  if (isset($args->get_nest_data)) {
  if ($args->get_nest_data === true) { getNests($args); }
  }
}
function getInstanceData($args) {
  global $db;
  $sql_instancedata = "SELECT data, type FROM instance WHERE name = :name";
  if (isset($args->instance_name)) {
    $stmt = $db->prepare($sql_instancedata);
    $stmt->bindValue(':name', $args->instance_name, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();
    $result['data'] = json_decode($result['data']);
    echo json_encode($result);
  } else {
    echo json_encode(array('status'=>'Error: no instance name?'));
    return;
  }
}
function getInstanceNames($args) {
  global $db;
  $sql_instances = "SELECT name, type FROM instance";
  $result = $db->query($sql_instances)->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
}
function getSpawnData($args) {
  global $db;
  $binds = array();
  if (isset($args->spawns) || isset($args->stops)) {
    if (isset($args->spawns) && count($args->spawns) > 0) {
      $spawns_in  = str_repeat('?,', count($args->spawns) - 1) . '?';
      $binds = array_merge($binds, $args->spawns);
    }
    if (isset($args->stops) && count($args->stops) > 0) {
      $stops_in  = str_repeat('?,', count($args->stops) - 1) . '?';
      $binds = array_merge($binds, $args->stops);
    }
    if ($stops_in && $spawns_in) {
      $points_string = "(pokestop_id IN (" . $stops_in . ") OR spawn_id IN (" . $spawns_in . "))";
    } else if ($stops_in) {
      $points_string = "pokestop_id IN (" . $stops_in . ")";
    } else if ($spawns_in) {
      $points_string = "spawn_id IN (" . $spawns_in . ")";
    } else {
      echo json_encode(array('spawns' => null, 'status'=>'Error: no points!'));
      return;
    }
    if (is_numeric($args->nest_migration_timestamp) && (int)$args->nest_migration_timestamp == $args->nest_migration_timestamp) {
      $ts = $args->nest_migration_timestamp;
    } else {
      $ts = 0;
    }
    $binds[] = $ts;
    if (is_numeric($args->spawn_report_limit) && (int)$args->spawn_report_limit == $args->spawn_report_limit && (int)$args->spawn_report_limit != 0) {
      $limit = " LIMIT " . $args->spawn_report_limit;
    } else {
      $limit = '';
    }
    $sql_spawn = "SELECT pokemon_id, COUNT(pokemon_id) as count FROM pokemon WHERE " . $points_string . " AND first_seen_timestamp >= ? GROUP BY pokemon_id ORDER BY count DESC" . $limit;
    $stmt = $db->prepare($sql_spawn);
    try {
     $stmt->execute($binds);
     } catch (PDOException $e) {
      //let_dump($e);
      let_dump(array('sql_spawnpoint' => $sql_spawn));
      let_dump(array('binds_count' => count($binds), 'stop_count' => count($args->stops), 'spawn_count' => count($args->spawns)));
      let_dump($args);
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  echo json_encode(array('spawns' => $result));
}
function setNestData($args) {
  global $mdb;
  $binds = array();
  $sql_nests = "INSERT INTO nests (pokemon_id, updated, name, pokemon_count, pokemon_avg, nest_id, lat, lon, polygon_path) VALUES (?,?,?,?,?,?,?,?,?) on DUPLICATE KEY UPDATE pokemon_id = ?, updated = ?, name = ?, pokemon_count = ?, pokemon_avg = ?";
  $stmt = $mdb->prepare($sql_nests);  
  $result = $stmt->execute(array_merge($binds, [$args->nest_pokemon, $args->updated, $args->name, $args->pokemon_count, $args->avg_spawns, $args->nest_id, $args->lat, $args->lon, $args->path, $args->nest_pokemon, $args->updated, $args->name, $args->pokemon_count, $args->avg_spawns]));
  echo $result;
}
function getNests($args) {
  global $mdb;
  $sql_import_nests = "SELECT name, nest_id, lat, lon, polygon_path FROM nests";
  $stmt = $mdb->prepare($sql_import_nests);   
  $stmt->execute();
  $import_nests = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(array('nests' => $import_nests));
}
function getData($args) {
  global $db;
  $binds = array();
  
  if ($args->show_gyms === true) {
    $sql_gym = "SELECT id, lat, lon as lng, ex_raid_eligible as ex, name, updated FROM gym WHERE lat > ? AND lon > ? AND lat < ? AND lon < ?";
    $stmt = $db->prepare($sql_gym);
    $stmt->execute(array_merge($binds, [$args->min_lat, $args->min_lng, $args->max_lat, $args->max_lng]));
    $gyms = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  if ($args->show_pokestops === true) {
    $sql_pokestop = "SELECT id, lat, lon as lng, name, updated, deleted FROM pokestop WHERE lat > ? AND lon > ? AND lat < ? AND lon < ?";
    $stmt = $db->prepare($sql_pokestop);
    $stmt->execute(array_merge($binds, [$args->min_lat, $args->min_lng, $args->max_lat, $args->max_lng]));
    $stops = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  if ($args->show_quests === true) {
    $sql_quest = "SELECT id, lat, lon as lng, name, deleted FROM pokestop WHERE quest_type is NULL AND lat > ? AND lon > ? AND lat < ? AND lon < ?";
    $stmt = $db->prepare($sql_quest);
    $stmt->execute(array_merge($binds, [$args->min_lat, $args->min_lng, $args->max_lat, $args->max_lng]));
    $quests = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  if ($args->show_spawnpoints === true) {
    $sql_spawnpoint = "SELECT id, despawn_sec, lat, lon as lng, updated FROM spawnpoint WHERE lat > ? AND lon > ? AND lat < ? AND lon < ?";
    $stmt = $db->prepare($sql_spawnpoint);
    $stmt->execute([$args->min_lat, $args->min_lng, $args->max_lat, $args->max_lng]);
    $spawns = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  echo json_encode(array('gyms' => $gyms, 'pokestops' => $stops, 'quests' => $quests, 'spawnpoints' => $spawns ));
}
function getOptimization($args) {
  global $db;
  if (isset($args->points)) {
    $points = $args->points;
  } else {
    echo json_encode(array('status'=>'Error: no points'));
    return;
  }
  $best_attempt = array();
  for($x=0; $x<$args->optimization_attempts;$x++) {
    shuffle($points);
    $working_gyms = $points;
    $attempt = array();
    while(count($working_gyms) > 0) {
      $gym1 = array_pop($working_gyms);
      foreach ($working_gyms as $i => $gym2) {
        $dist = haversine($gym1, $gym2);
        if ($dist < $args->circle_size) {
          unset($working_gyms[$i]);
        }
      }
      $attempt[] = $gym1;
    }
    if(count($best_attempt) == 0 || count($attempt) < count($best_attempt)) {
      $best_attempt = $attempt;
    }
  }
  if ($args->do_tsp) {
    $working_gyms = $best_attempt;
    $index = rand(0,count($working_gyms)-1);
    $gym1 = $working_gyms[$index];
    while(count($working_gyms) > 0) {
      unset($working_gyms[$index]);
      $final_attempt[] = $gym1;
      unset($working_gyms[$index]);
      foreach ($working_gyms as $i => $gym2) {
        $dist = haversine($gym1, $gym2);
        while ($distances[$dist]) {
          $dist++;
        }
        $distances[$dist] = $gym2;
        $index = $i;
      }
      ksort($distances);
      $closest_gym = array_shift($distances);
      $gym1 = $closest_gym;
    }
    $best_attempt = $final_attempt;
  }
  echo json_encode(array('bestAttempt' => $best_attempt));
}
function haversine($gym1, $gym2) {
  $r = 6371000;
  $latFrom = ($gym1->lat * M_PI / 180);
  $lngFrom = ($gym1->lng * M_PI / 180);
  $latTo = ($gym2->lat * M_PI / 180);
  $lngTo = ($gym2->lng * M_PI / 180);
  $latDelta = $latTo - $latFrom;
  $lngDelta = $lngTo - $lngFrom;
  $a = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
      cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
  return $a * $r;
}
?>