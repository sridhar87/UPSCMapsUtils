<?php 
require_once 'fbaccess.php';
if (!$user):
require_once 'logout.php';
else:
?>

<html>
<head>
<title>maps++ | Maps for IAS/IPS/IFS Exams</title>

<style type="text/css">


.tabs li {
	list-style: none;
	display: inline;
}


.tabs a {
    /* background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #5cb811), color-stop(1, #77d42a) );
	background:-moz-linear-gradient( center top, #5cb811 5%, #77d42a 100% );
	/* filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#5cb811', endColorstr='#77d42a'); */
	background-color:#c9efab;
	padding: 5px 10px;
	display: inline-block;
	color: #000;
	border: 1px solid #268a16;
	/* background: #666; 
	color: #fff; */
	text-decoration: none;
}

.tabs a.active {
    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #5cb811), color-stop(1, #77d42a) );
	background:-moz-linear-gradient( center top, #5cb811 5%, #77d42a 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#5cb811', endColorstr='#77d42a');
	background-color:#5cb811;
	color: #000;
	border:3px solid #268a16;
	border-bottom: none;
	/* background: #fff;
	color: #000; */
}

.MyMapListClass {
	border: 1px solid #268a16;
	padding: 1px 4px;
	border-radius: 15px;
	-moz-border-radius: 15px;
}

/* The following imageless css button was generated by CSSButtonGenerator.com */
.NewMapButton {
    -moz-box-shadow:inset 0px 1px 0px 0px #caefab;
	-webkit-box-shadow:inset 0px 1px 0px 0px #caefab;
	box-shadow:inset 0px 1px 0px 0px #caefab;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #77d42a), color-stop(1, #5cb811) );
	background:-moz-linear-gradient( center top, #77d42a 5%, #5cb811 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#77d42a', endColorstr='#5cb811');
	background-color:#77d42a;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #268a16;
	display:inline-block;
	color:#306108;
	font-family:arial;
	font-size:15px;
	font-weight:bold;
	padding:1px 4px;
	text-decoration:none;
	text-shadow:1px 1px 0px #aade7c;
}.NewMapButton:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #5cb811), color-stop(1, #77d42a) );
	background:-moz-linear-gradient( center top, #5cb811 5%, #77d42a 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#5cb811', endColorstr='#77d42a');
	background-color:#5cb811;
}.NewMapButton:active {
	position:relative;
	top:1px;
}

.myMapsTabsClass ul {
	text-align: left;
	list-style: none;
	padding: 0;
	margin: 0 auto;
}

.myMapsTabsClass li {
	display: block;
	margin: 0;
	padding: 0;
}

.myMapsTabsClass li a {
	display: block;
	padding: 0.5em 0 0.5em 2em;
	border-width: 1px;
	border-color: #ffe #aaab9c #ccc #fff;
	border-style: solid;
	color: #777;
	text-decoration: none;
	background: #f7f2ea;
}

;
.myMapsTabsClass li a.active {
	background: transparent;
	color: #800000;
}

.myMapsTabsClass li a:hover {
	color: #800000;
	background: transparent;
	border-color: #aaab9c #fff #fff #ccc
}
</style>
<script
	src="js/jquery-1.8.2.js"></script>

<script
	src="http://maps.google.com/maps/api/js?key=AIzaSyByDaJQtdfxMBDxYRXVQqISAXCgCqSKul0&sensor=false"
	type="text/javascript">
    </script>
<script src="js/MarkerWithLabel.js" type="text/javascript"></script>
<script src="js/json-parse.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.0.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="static/css/jquery-ui-1.9.0.custom.min.css"/>
<style type="text/css">
.labels {
	color: red;
	background-color: white;
	font-family: "Lucida Grande", "Arial", sans-serif;
	font-size: 10px;
	font-weight: bold;
	text-align: center;
	border: 2px solid black;
	white-space: nowrap;
}

.createMapOverlayClass {
     position: absolute;
     left: 30px;
     top: 165px;
     width: 30%;
     height:20%;
     text-align:left;
     background-color: white;
}

div.map-popup {
    overflow: auto;
    overflow-x: hidden;
    overflow-y: auto;
}

</style>

<script language="javascript" type="text/javascript">
 //<![CDATA[

//Globals
 //initialise the map 
 var map;
 var currentMapName;
 var currMarker;
 var placesMarked = [];
 var outlineMapStyleOpts;
 var modalId; // Id of open modal window.
 var infoWindow;
 
function fillWindow() {
	var mapDiv = document.getElementById("mapDiv");
	var infoDiv = document.getElementById("infoDiv");
	try{
		if (window.innerHeight) { //if browser supports window.innerWidth
			mapDiv.style.height = window.innerHeight+'px';
			mapDiv.style.width = window.innerWidth-300+'px';
			infoDiv.style.height = window.innerHeight+'px';
		}
		else{	//MSIE
			document.body.scroll="no";
			mapDiv.style.height = document.body.clientHeight+'px';
			mapDiv.style.width = document.body.clientWidth-300+'px'; 
			infoDiv.style.height = document.body.clientHeight+'px';
        }
	}
	catch(ex){
	}
}

function reloadMap(name) {
	if (!map) {
		var mapOptions = {
				  mapTypeControl: false,
				  mapTypeControlOptions: {
					  mapTypeIds: ['outline'],
				      position: google.maps.ControlPosition.TOP_LEFT
				  },
		          center: new google.maps.LatLng(22.0, 81.0),
		          zoom: 5,
		          mapTypeId: 'outline'
		        };
		outlineMapStyleOpts = [ 
		                       { 
		                   	"featureType": "administrative", "elementType": "labels", "stylers": 
		                       	[ { "visibility": "off" } ] 
		           			},
		           			{ "featureType": "landscape", "elementType": "geometry", "stylers": 
		               			[ { "visibility": "on" } ] 
		           			},
		               		{ "featureType": "road", "elementType": "geometry", "stylers":
		                   		 [ { "visibility": "off" } ] 
		              		    },
		              		    { "featureType": "poi", "elementType": "labels", "stylers": 
		                  		    [ { "visibility": "off" } ] 
		              		    },
		              		    { "featureType": "landscape.natural", "stylers":
		              		        [ { "weight": 0.1 }, { "color": "#ffffff" } ] 
		              		    },
		          		        { "featureType": "poi.park", "stylers": 
		          	   		        [ { "color": "#ffffff" } ] 
		       	   		    } 
		       	   ];
		map = new google.maps.Map(document.getElementById("mapDiv"), mapOptions);
		map.mapTypes.set('outline', new google.maps.StyledMapType(outlineMapStyleOpts, { name: name }));
	}
	currMarker = new google.maps.Marker({
        position: map.getCenter(),
        map: map
      });
    currentMapName = name;
   google.maps.event.addListener(map, 'click', function(event) {
      currMarker.setPosition(event.latLng);
      currMarker.setVisible(true);
      currMarker.setTitle("Click again to add details about the place");
      if (document.getElementById("infoWindowPopupLat")) {
    	  document.getElementById("infoWindowPopupLat").value = Math.ceil(currMarker.getPosition().lat() * 100)/100;
      }
      if (document.getElementById("infoWindowPopupLng")) {
    	  document.getElementById("infoWindowPopupLng").value = Math.ceil(currMarker.getPosition().lng() * 100)/100;
      }
   });
   google.maps.event.addListener(currMarker, 'click', function(event) {
	   var html = "<div id=\"\">";
	   var lat = Math.ceil(currMarker.getPosition().lat() * 100.0)/100;
	   var lng = Math.ceil(currMarker.getPosition().lng() * 100.0)/100;
	// this dom node will act as wrapper for our content
	   var wrapper = document.createElement("div");

	   // fixed height only :P
	   wrapper.style.height = "250px";
	   
	   html += "<form id=\"addPlaceForm\">";
	   html += "<label>Map Name</label><input type=\"text\" name=\"mapName\" value=\"" + currentMapName + "\" readOnly=\"readOnly\"/>";
	   html += "<label>Place Name</label>";
	   html += "<input type=\"text\" name=\"placeName\"/><br>";
	   html += "<input id=\"infoWindowPopupLat\" type=\"hidden\" width=6 readOnly=\"readOnly\" name=\"lat\" value=\"" + lat +
	   					"\"/><input id=\"infoWindowPopupLng\" readOnly=\"readOnly\" type=\"hidden\" width=6 name=\"lng\" value=\"" + lng + "\"/><br>";
	   html += "<label>Description:</label><textarea rows=6 cols=50 name=\"placeDescription\" maxlength=300></textarea><br>"; 
	   html += "<button type=\"submit\" onclick=\"return addPlace()\" name=\"Add\">Add Place</button>";
	   html += "<img src=\"static/images/ajax-loader.gif\" style=\"visibility: hidden\" id=\"addPlaceAjaxLoader\" />";
	   html += "<div id=\"addPlaceResponse\"></div>";
	   html += "</form>";
	   html += "</div>";
	   if (infoWindow) {
		   infoWindow.close();
	   }
		// inject markup into the wrapper
	   wrapper.innerHTML = html;

	   // style containing overflow declarations
	   wrapper.className = "map-popup";
	   
	   infoWindow = new google.maps.InfoWindow({
		    content: wrapper 
	   });
	   infoWindow.open(map, currMarker);
   });
}

function addPlace() {
	document.getElementById("addPlaceAjaxLoader").style.visibility='visible';
	$.ajax({
        type: "POST",
        url: "addPlaceToUserMap.php",
        data: $("#addPlaceForm").serialize(), // serializes the form's elements.
        success: function(data)
        {
            alert(data);
            document.getElementById("addPlaceAjaxLoader").style.visibility='hidden';
            var jsonData = json_parse(data);
            var message = "";
            if (jsonData.responseCode == 200) {
            	message = "<font size=3 color=green>" + jsonData.message + "</font>";
            } else {
            	message = "<font size=3 color=red>" + jsonData.message + "</font>";
            }
            $("#addPlaceResponse").html(message);
            
            setTimeout(function() { if (infoWindow.id) infoWindow.close(); }, 10000);
        }
	});
	return false;
}


function load()
{
	    fillWindow();
	    reloadMap("Outline Map");
}
 
 function clearMap() {
   currMarker.setVisible(false);
   if (placesMarked != null) {
     for( var i in placesMarked) {
       placesMarked[i].setMap(null);
     }
     placesMarked = null;
   }
   return true;
 }

 var placesCache={};
 
 function getPlaces(selectedMap, args) {
   var places = {
     MetropolitanCities: [
      {
        name: "Delhi",
      	latLng: new google.maps.LatLng(29.01, 77.38)
      },
      {
        name: "Kolkata",
      	latLng: new google.maps.LatLng(22.56, 88.36)
      },
      {
          name: "Mumbai",
          latLng: new google.maps.LatLng(18.96, 72.82)
      },
      {
          name: "Chennai",
       	  latLng: new google.maps.LatLng(13.08, 80.27)
      },
      {
          name: "Hyderabad",
          latLng: new google.maps.LatLng(17.36, 78.46)
      },
      {
          name: "Bangalore",
       	  latLng: new google.maps.LatLng(12.98, 77.58)
      }
     ], 
     NuclearPowerPlants: [
		{
		    name: "Tarapur",
		  	latLng: new google.maps.LatLng(19.49, 72.39)
		  },
		  {
		    name: "Kalpakkam",
		  	latLng: new google.maps.LatLng(12.50, 80.15)
		  },
		  {
		      name: "Narora",
		      latLng: new google.maps.LatLng(27.5, 78.43)
		  },
		  {
		      name: "Kudankulam",
		   	  latLng: new google.maps.LatLng(8.163, 77.71)
		  }
     ],
     IndusValleyCivilization: [
      {
        name: "Mohenjadaro",
      	latLng: new google.maps.LatLng(27.32, 68.13)
      },
      {
          name: "Harappa",
          latLng: new google.maps.LatLng(30.63, 72.88)
       }
     ],
     MangrovesInIndia: [
		{
		 name: "Pichavaram",
		 latLng: new google.maps.LatLng(11.43, 79.77)
		},
		{
		 name: "Sundarbans",
		 latLng: new google.maps.LatLng(21.94, 88.9)
		},
		{
		 name: "Bhitarkanika",
		 latLng: new google.maps.LatLng(20.67, 87.0)
		}      
     ]
   };
   return places[selectedMap];
 }

 var mapsCached={};

 function loadCachedPlaces(selectedMap) {
	 places = mapsCached[selectedMap];
	 clearMap();
	 if (placesMarked == null) {
		    placesMarked = [];
     } else {
	    placesMarked = null;
	    placesMarked = [];
	 }
	 for (var i in places) {
		var placeMarker = new MarkerWithLabel({
			position: new google.maps.LatLng(places[i].lat[0], places[i].lng[0]),
			map: map,
			title: places[i].placeDescription[0],
			labelContent: places[i].PlaceName[0],
			labelAnchor: new google.maps.Point(30, 0),
			labelClass: "labels",
			labelStyle: {opacity: 0.75}
	 	});
		placesMarked.push(placeMarker);
	}
 }

 function heading(text) {
	 return "<b>" + text + "</b>";
 }
 
 function loadPlaces(mapName) {
   var selectedMap = mapName;
   if (!mapsCached[selectedMap]) {
	   $("#mapLoadAjaxLoader").show();
	   $("#mapHeadingText").html(heading(selectedMap));
	   $.get("listUserPlaces.php", {mapName: selectedMap} , function(data) {
			 var jsonData = json_parse(data);
			 if (jsonData[0].places) {
				 mapsCached[selectedMap] = jsonData[0].places;
				 loadCachedPlaces(selectedMap);
			 }
			 $("#mapLoadAjaxLoader").hide();
		 });
   } else {
	   $("#mapLoadAjaxLoader").show();
	   $("#mapHeadingText").html(heading(selectedMap));
	   loadCachedPlaces(selectedMap);
	   $("#mapLoadAjaxLoader").hide();
   }
 }

 var practicePlaces=[]
 var practiceMapMarker;
 var practicePlaceNumber = 0;
 var practicePlaceAsked;
 
 var practiceScore=0;
 var practiceMaxScore=0;
 var included = false;

 function checkAnswer() {
	 practiceMapMarker=currMarker;
	 var tolerableError = 1;
	 if (practiceMapMarker.getPosition() != null) {
		 var error = Math.pow(practiceMapMarker.getPosition().lat() - practicePlaceAsked.latLng.lat(), 2.0) 
		 				+ Math.pow(practiceMapMarker.getPosition().lng() - practicePlaceAsked.latLng.lng(), 2.0);  
		 if (error <= tolerableError) {
			 if (!included)
			 	practiceScore++;
			 alert("Congratulations!! Your answer is correct, your score is now: " + practiceScore);
			 return true;
		 } else {
			 alert("Your answer is wrong, click cancel to try again");
			 return false;
		 }
		 return false;
	 }
 }
 
 function showPlace() {
	 if (practicePlaceNumber < practicePlaces.length) {
		 included = false;
		 var newHtml = "<p>Place: " + practicePlaces[practicePlaceNumber].name;
		 newHtml += "<form><input type=\"button\"  name=\"Submit\" value=\"Next\"" +
		 			"onClick=\"var r = checkAnswer(); if (r) showPlace(); return true;\"></form>";
		 document.getElementById('sampleMapPracticePlace').innerHTML = newHtml;
		 practicePlaceAsked = practicePlaces[practicePlaceNumber];
		 practicePlaceNumber++;
	 } else {
		 var newHtml = "<p>Your score is " + practiceScore + "/" + practiceMaxScore
		 					+ ". Select a different map and press 'Go' to take another test. <p>";
		 document.getElementById('sampleMapPracticePlace').innerHTML = newHtml;
	 }
 }

 function clearPracticeVars() {
	 practicePlaces=[]
	 practiceMapMarker=null;
	 practicePlaceNumber = 0;
	 practicePlaceAsked = null;
	 practiceScore=0;
	 practiceMaxScore=0;
	 document.getElementById('sampleMapPracticePlace').innerHTML = "";
 }

 function loadPlacesForPractice() {
	   clearPracticeVars();
	   var selectMapOption = document.getElementById("sampleMapPracticeDropDown");
	   var selectedMap = selectMapOption.options[selectMapOption.selectedIndex].value;
	   if (placesMarked == null) {
	    placesMarked = [];
	   } else {
	    placesMarked = null;
	    placesMarked = [];
	   }
	   practicePlaces = getPlaces(selectedMap);
	   practiceMaxScore = practicePlaces.length;
	   clearMap();
	   showPlace();
  }

  function loadNextSetOfMaps(lastProcessed, from) {
	  $("#myMapsAjaxLoader").show();
	  $.get('listUserMaps.php', {lastProcessedMap: lastProcessed}, function(data) {
		  parseAndFillDocument(data, {"reqTab" : from});
	  });
  }


 function loadMap(mapName) {
	 reloadMap(mapName);
	 loadPlaces(mapName);
 }

 function replaceAll(string, replace, wit) {
	 var result = "";
	 for(var i=0;i<string.length;i++) {
		 if (string[i]==replace[0]) result += wit;
		 else result += string[i]; 
	 }
	 return result;
 }

 function getTr(userMapName, userMapDescription) {
	 var moddedUserName=replaceAll(userMapName, " ", ",");
	 var tr = "<tr><td class=\"MyMapListClass\" style=\"width: 250px;\" onmouseout=\"this.style.background='transparent';\"  onmouseover=\"this.style.background='#77d42a'; this.style.cursor='pointer';\""
     +  " onclick=\"loadMap('"  + userMapName + "')\"><a style=\"text-decoration: none; color: black;\" href=\"#\">"	 + userMapName + "<br> <font size=1><i>(" 
	  + userMapDescription + ")</i></font></a>" +  "</td></tr>";
	  return tr;
 }

 function addMapToList(userMapName, userMapDescription, args) {
	 if (document.getElementById("myMapsList")) {
		 $("#myMapsList tbody").append(getTr(userMapName, userMapDescription));
	 } else {
		var table="<table id=\"myMapsList\" border=0>";
		table += getTr(userMapName, userMapDescription);
		table+="</table>";
		$("#myMaps").html(table);
	 }
 }

 function parseAndFillDocument(data, args) {
	    var available = false;
	 	$("#myMapsAjaxLoader").hide();
	 	var matchStr = null;
	 	var from = "myMaps";
		if (args) {
			if (args["matchStr"]) {
				matchStr = args["matchStr"]; 
			}
			if (args["reqTab"]) {
				from = args["reqTab"];
			}
		}
		var html = "";
		var jsonData = json_parse(data);
		var count=0;
		if (jsonData.maps) {
			var table="<table id=\"" + from + "List\" border=0>";
			var listOfUserMaps = jsonData.maps;
			var lastProcessed = "";
			for (var i=0;i<listOfUserMaps.length;i++) {
				var mapName=listOfUserMaps[i].mapName[0];
				if (mapName == matchStr) {
					available = true;
				}
				var mapDescription = listOfUserMaps[i].mapDescription[0];
				table+=getTr(mapName, mapDescription);;
				lastProcessed = mapName;
				count++;
			}
			//table+="<tr><td><div id=\"AddMapButton\"><button type=\"button\" onClick=\"return createMapWithDescription()\" name=\"NewMap\">New Map</button></div></td></tr>";
			table+="</table>";
			html += table;
			if (listOfUserMaps.length >= jsonData.maxMaps) {
				html += "<button style=\"float: right;\" type=\"button\" onclick=\"loadNextSetOfMaps('" + lastProcessed + "', '" + from +"')\">View more maps</button>";
			}
		}
		$("#" + from + "Result").html(html);
		//$("#myMaps").addClass("mapListClass");
		return {available: available, count: count};
 }

 var selectedTab = null;
 var loaded = {};

 function loadTab(selected) {
	 if (!loaded[selected]) {
		 $("#myMapsAjaxLoader").show();
		 $.get('listUserMaps.php', function(data) {
				var result = parseAndFillDocument(data, {"reqTab": selected});
				if (result.count == 0) {
					var html = "";
					if (selected == "myMapsTab") {
						var createMapLink = "<a onclick=\"return createMapWithDescription()\" href=\"#\">Create a new map</a>";
						html = "<font size=2><i>You do not have any maps, " + (selected == "myMapsTab" ? createMapLink : "") 
						 	+ "</i></font>";
					} else if (selected == "otherMapsTab") {
						var suggestMapLink = "<a onclick=\"suggestMapWithDescription()\" href=\"#\">Suggest a new map</a>";
						html = "<font size=2><i>There are no maps, " + suggestMapLink + "</i></font>";
					}
					$("#" + selected + "Result").html(html);
				}
				loaded[selected] = true;
		 });
	 }
 }

//Wait until the DOM has loaded before querying the document
	$(document).ready(function() {
		$('ul.tabs').each(function() {
			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active, $content, $links = $(this).find('a');

			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			act = $links.filter('[href="'+location.hash+'"]')[0] || $links[0];
			$active = $(act);
			selectedTab = act;
			$active.addClass('active');
			$content = $($active.attr('href'));

			// Hide the remaining content
			$links.not($active).each(function () {
				$($(this).attr('href')).hide();
			});

			loadTab(selectedTab.id);

			// Bind the click event handler
			$(this).on('click', 'a', function(e){
				// Make the old tab inactive.
				$active.removeClass('active');
				$content.hide();

				// Update the variables with the new link and content
				$active = $(this);
				$content = $($(this).attr('href'));
				selectedTab = this;

				// Make the tab active.
				$active.addClass('active');
				$content.show();

				// Prevent the anchor's default click action
				e.preventDefault();

				loadTab(selectedTab.id);
			});
		});

		$("#mapSearchInput").keyup(function(event){
			if (event.keyCode == 13) {
				var searchInput = document.getElementById("mapSearchInput").value;
				$("#myMapsAjaxLoader").show();
				var available = false;
				$.get('listUserMaps.php', {query: searchInput}, function(data) {
				  var id = selectedTab.id;
				  var result = parseAndFillDocument(data, {"matchStr": searchInput, "reqTab": id});
				  if (!result.available) {
					  createMapLink = "Do you want to <a onclick=\"return createMapWithDescription()\" href=\"#\">Create this map?</a>";
					  var html="<font size=2><i>This map is not available. " + (id == "myMapsTab" ? createMapLink : "") 
					  						 + "</i></font>";
					  $("#" + id + "SearchOutput").html(html);
					} else {
					  $("#" + id + "SearchOutput").html("");
				    }
				});
			}
		});

		$("#searchAddress").keyup(function(event){
			if (event.keyCode == 13) {
				mapSearch();
			}
		});

		$(document).keyup(function(e){
            if(e.keyCode == 27){
                if (modalId != null) {
                    $("#transparentDiv").hide();
                    modalId=null;
                }
            }
        });

	});

	function createMapWithDescription() {
		var createMapOverlayHtml = "";
		mapSearchInput = document.getElementById("mapSearchInput").value;
		createMapOverlayHtml += "<form id=\"createMapOverlayForm\"><fieldset><legend>Create Map</legend>";
		createMapOverlayHtml += "<label>Map Name</label><input type=\"text\" name=\"mapName\" value=\"" + mapSearchInput + "\"/><br>";
		createMapOverlayHtml += "<label>Description</label><input type=\"text\" name=\"mapDescription\"/>";
		createMapOverlayHtml += "<input type=\"submit\" onClick=\"return createMap()\" id=\"createMapSubmitButton\" name=\"Create Map\"/></fieldset></form>";
		createMapOverlayHtml += "[Press Esc to go back]";
		createMapOverlayHtml += "<img src=\"static/images/ajax-loader.gif\" style=\"visibility: hidden\" id=\"createMapAjaxLoader\" />";
		$("#modalDiv").html(createMapOverlayHtml);
		$("#modalDiv").attr("class", "createMapOverlayClass");
		$("#transparentDiv").show();
		modalId = "modalDiv";
	}

	function createMap() {
		var url = "createMap.php"; // the script where you handle the form input.
		document.getElementById("createMapAjaxLoader").style.visibility='visible';
	    $.ajax({
	           type: "POST",
	           url: url,
	           data: $("#createMapOverlayForm").serialize(), // serializes the form's elements.
	           success: function(data)
	           {
		           jsonData = json_parse(data);
		           var message = "";
		           if (jsonData.resultCode == 500) {
			           // error
			           message = "<font size=3 color=red>" + jsonData.error + "</font>";
		           } else {
			           message = "<font size=3 color=green>" + jsonData.success + "</font>";
			           addMapToList(jsonData.map[0].mapName[0], jsonData.map[0].mapDescription[0]);
		               reloadMap(jsonData.map[0].mapName[0]);
		           }
		           
	               $("#createMapResponse").html(message); // show response from the php script.
	               setTimeout(function() { $("#createMapResponse").html(""); }, 7000);
	               $("#transparentDiv").hide();
	               modalId = null;
	           }
         });
        return false;
	}

	function mapSearch() {
		$("#googleMapsSearchAjaxLoader").show();
		var addressField = document.getElementById('searchAddress');
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode(
		        {'address': addressField.value}, 
		        function(results, status) {
			        var html = "";
		            if (status == google.maps.GeocoderStatus.OK) {
		                var loc = results[0].geometry.location;
		                if (currMarker == null) {
		                	currMarker = new google.maps.Marker({
		                        position: loc,
		                        map: map,
		                        title: addressField.value,
		                        visible: true
		                    });
		                }
		                currMarker.setPosition(loc);
		                currMarker.setVisible(true);
		                map.setCenter(loc);
		                html = "<font size=2 color=green><i>" + results.length + " results found for "+ addressField.value + "</i></font>";
		                // use loc.lat(), loc.lng()
		            }
		            else {
		                html = "<font size=2 color=red><i>" + addressField.value + " could not be found</i></font>"
		            }
		            $("#mapSearchResult").html(html);
		            setTimeout(function() { $("#mapSearchResult").html("");}, 50000);
		        }
		  );
		$("#googleMapsSearchAjaxLoader").hide();
	}

</script>
</head>

<body onload="load()" style="background-color: #ddf7c6; height: 100%; margin: 0; padding: 0;">
	<div id="infoDiv"
		style="overflow: auto; border-width: 0px; position: absolute; left: 5px; top: 0px; width: 290px; height: 100%;">
		Welcome,
		<?= $user_profile['name']?>
		<a href=<?= $logoutUrl ?>>Logout</a>
		<div id="logo" style="position: absolute; left: 0px; top: 20px; width: 285px; height: 100px;">
	       <img src="static/images/Map++.jpg"/>
		</div>
		<div id='MapsDiv' style="overflow: auto; position: absolute; left: 5px; top: 130px; width: 285px">
			<div id="mapSearch">
    			<label for="mapSearchInput"><b>Search</b></label>
    			<input type="text" id="mapSearchInput"/>
    			<img src="static/images/ajax-loader.gif" id="myMapsAjaxLoader" style="display: none" />
    		</div>
    		<div id="mapSearchOutputDiv">
    			<ul class='tabs'>
    				<li><a id="myMapsTab" href='#myMapsTabDiv'>My Maps</a></li>
    				<li><a id="otherMapsTab" href='#otherMapsTabDiv'>Other Maps</a></li>
    			</ul>
    			<div id="myMapsTabDiv">
    			   <div id="myMapsTabSearchOutput"></div>
    			   <div id="myMapsTabResult"></div>
    			   
    			   <div id="myMapsOptions" style="text-align: center;">
    			   <span><a id="BackPageButton" href="#" class="NewMapButton" title="Back" style="display: none;">&lt;&lt;</a></span>
    			   <span><a id="AddNewMapButton" href="#" class="NewMapButton" title="Create a new Map">+</a></span>
    			   <span><a id="NextPageButton" href="#" class="NewMapButton" title="Next" style="display: none;">&gt;&gt;</a></span>
    			   </div>
    			</div>
    			<div id="otherMapsTabDiv">
    				<div id="otherMapsTabSearchOutput"></div>
    				<div id="otherMapsTabResult"></div>
    			</div>
    		</div>
    		<div id="createMapOverlay"></div>
    		<div id="createMapResponse"></div>
		</div>
	</div>
	<div id="mapDiv"
		style="position: absolute; left: 300px; top: 0px; height: 100%"></div>
	<div id="mapHeading" style="position:absolute; left: 400px; top: 10px; zIndex=10;">
		<span id="mapHeadingText" style="background: #ddf7c6;"></span>
		<span><img src="static/images/ajax-loader.gif" id="mapLoadAjaxLoader" style="display: none" /></span>
	</div>
	<div id="mapSearchDiv" style="position: relative; float: right; top: 0px; height: 40px; zIndex=10;">
		<input type="text" id="searchAddress" value=""/>
		<button onclick="mapSearch();">Search In Map</button>
		<img src="static/images/ajax-loader.gif" id="googleMapsSearchAjaxLoader" style="display: none" />
		<div id="mapSearchResult"></div>
	</div>
	
	<div id="transparentDiv" style="display: none; background: transparent; position: absolute; left: 0px; top: 0px; height:100%; width: 100%">
		<div id="modalDiv">
		</div>
	</div>

</body>

</html>

<?php endif; 
?>