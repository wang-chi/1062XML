<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
        .container{
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        .blog-header-logo {
            font-family: "Playfair Display", Georgia, "Times New Roman", serif;
            font-size: 2.25rem;
        }
        @mixin text-emphasis-variant($parent, $color) {
            #{$parent} {
                color: $color !important;
            }
            a#{$parent} {
                @include hover-focus {
                    color: darken($color, 10%) !important;
                }
            }
        }
    </style>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link href="https://cdn.bootcss.com/bootstrap-select/1.12.1/css/bootstrap-select.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
    <script>
        $(function(){
            $(".dropdown-menu-city").on('click', 'a', function(){
                $(".btn_city").text($(this).text());
                $(".btn_city").val($(this).text());
                var area = $(this).text();
                $.ajax({
                    url:"deal.php",				
                    method:"POST",
                    data:{
                        area:area
                    },					
                    success:function(res){	
                        $('.dropdown-menu-station').html(res);//處理回吐的資料
                        
                    }
                })//end ajax

                });
            });
        $(function(){
            $(".dropdown-menu-station").on('click', 'a', function(){
                $(".btn_station").text($(this).text());
                $(".btn_station").val($(this).text());
                var name = $(this).text();
                var Mlat, Mlng;
                $.ajax({
                    url:"getLatLng.php",				
                    method:"POST",
                    data:{
                        name: name
                    },					
                    success:function(res){	
                        console.log(res);
                        var tmp = res.split(',');
                        updateMap(tmp[0],tmp[1]);
                        getTourDetail(name);
                    }
                })//end ajax
                });
            });
        function getTourDetail(name){
            $.ajax({
                    url:"getTourDetail.php",				
                    method:"POST",
                    data:{
                        name: name
                    },					
                    success:function(res){	
                        console.log(res);
                        document.getElementById("tourdetail").style.visibility = "visible"; 
                        $('tbody').html(res);//處理回吐的資料
                    }
                })//end ajax
        }
    </script>
</head>
<?php 
    // Opens a connection to a MySQL server
    require("phpsqlajax_dbinfo.php");
    $connection=mysqli_connect ('localhost', $username, $password, $database);
    $connection->set_charset("utf8")
?>
<body>
    <div class="container">
    <header class="blog-header py-3">
        <div class="row flex-nowrap justify-content-between align-items-center">
          <div class="col-8 text-center">
            <a class="blog-header-logo text-dark">PM2.5火車站地圖</a>
          </div>          
        </div>
    </header>    
    <form>
        <div class="form-group row">
            <label for="staticStation" class="col-sm-2 col-form-label text-center">車站</label>
            <div class="col-2 dropdown">
                <button class="btn btn-secondary dropdown-toggle  btn_city" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    選擇區域
                </button>
                <div class="dropdown-menu dropdown-menu-city" aria-labelledby="dropdownMenuButto">
                    <?php
                        $Query="SELECT DISTINCT city FROM station WHERE city!=\"\" ORDER BY lat DESC ";
                        $Result = mysqli_query($connection,$Query);
                        while($row = @mysqli_fetch_assoc($Result))
                        {
                            echo "<a class=\"dropdown-item\" value=\"".$row['city']."\">".$row['city']."</a>";
                        }
                    ?>
                </div>
            </div>
            <div class="col-2 dropdown">
                <button class="btn btn-secondary dropdown-toggle btn_station" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    選擇車站
                </button>
                <div class="dropdown-menu dropdown-menu-station" aria-labelledby="dropdownMenuButton">
                    <!-- <?php
                        // $Query="SELECT ID, StationName FROM station WHERE city=\"".$area."\"";
                        // $Result = mysqli_query($connection,$Query);
                        // while($row = @mysqli_fetch_assoc($Result))
                        // {
                        //     echo "<a class=\"dropdown-item\" value=\"".$row['ID']."\">".$row['StationName']."</a>";
                        // }
                    ?> -->
                </div>
            </div>
        </div>
    </form>
    <table class="table table-striped" id="tourdetail" style="visibility:hidden;">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">車次</th>
        <th scope="col">旅途名稱</th>
        <th scope="col">進站時間</th>
        <th scope="col">發車時間</th>
        <th scope="col">日期</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
    </table>

    <div id="map"></div>
    <script src="http://lib.sinaapp.com/js/jquery/3.1.0/jquery-3.1.0.min.js"></script>
    <script>
        var customLabel = {
                traffic: {
                    label: 'T'
                },
                school: {
                    label: 'S'
                }
            };

        function downloadUrl(url, callback) {
            var request = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest;

            request.onreadystatechange = function () {
                if (request.readyState == 4) {
                    request.onreadystatechange = doNothing;
                    callback(request, request.status);
                }
            };

            request.open('GET', url, true);
            request.send(null);
            }
        var map;
        
        function initMap() {
            console.log('initMap()')            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15
            });
            MoveToNowLocation();
            //  資訊視窗
            var infoWindow = new google.maps.InfoWindow;
            downloadUrl('echoXML.php', function (data) {
                var myLatlng = {
                    lat: 24.149,
                    lng: 120.68
                };
                /*
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 13,
                    center: myLatlng
                });
                */
                var xmlDoc = data.response;
                parser = new DOMParser();
                xml = parser.parseFromString(xmlDoc, "text/xml");
                var markers = xml.getElementsByTagName('marker');
                Array.prototype.forEach.call(markers, function (markerElem) {
                    var name = markerElem.getAttribute('name');
                    var address = markerElem.getAttribute('address');
                    var type = markerElem.getAttribute('type');
                    var airbox = markerElem.getAttribute('airbox');
                    var stationid = markerElem.getAttribute('stationid');
                    var pm25_1 = markerElem.getAttribute('pm25_1');
                    var pm25_5 = markerElem.getAttribute('pm25_5');
                    var pm25_10 = markerElem.getAttribute('pm25_10');
                    var point = new google.maps.LatLng(parseFloat(markerElem.getAttribute('lat')), parseFloat(markerElem.getAttribute('lng')));
                    var infowincontent = document.createElement('div');
                    infowincontent.id = stationid;
                    var strong = document.createElement('strong');
                    strong.textContent = name
                    infowincontent.appendChild(strong);
                    infowincontent.appendChild(document.createElement('br'));
                    var t_add = document.createElement('address');
                    t_add.textContent = address
                    infowincontent.appendChild(t_add);
                    var t_airbox_count = document.createElement('AirboxCount');
                    t_airbox_count.textContent = "周圍數量:" + airbox
                    infowincontent.appendChild(t_airbox_count);
                    infowincontent.appendChild(document.createElement('br'));
                    // PM2.5資料
                    var t_airbox = document.createElement('airbox');
                    t_airbox.textContent = "--PM2.5資訊--"
                    infowincontent.appendChild(t_airbox);
                    infowincontent.appendChild(document.createElement('br'));
                    
                    var t_airbox1 = document.createElement('airbox');
                    t_airbox1.id="airbox1";
                    t_airbox1.textContent = "1公里內:" + pm25_1 + "";
                    infowincontent.appendChild(t_airbox1);
                    infowincontent.appendChild(document.createElement('br'));
                    var t_airbox5 = document.createElement('airbox');
                    t_airbox5.id = "airbox5";
                    t_airbox5.textContent = "5公里內:" + pm25_5+ "";
                    infowincontent.appendChild(t_airbox5);
                    infowincontent.appendChild(document.createElement('br'));
                    var t_airbox10 = document.createElement('airbox');
                    t_airbox10.id = "airbox10";
                    t_airbox10.textContent = "10公里內:" + pm25_10+ "";
                    infowincontent.appendChild(t_airbox10);
                    infowincontent.appendChild(document.createElement('br'));
                    
                    var icon = customLabel[type] || {};
                    var marker = new google.maps.Marker({
                        map: map,
                        position: point,
                        label: icon.label
                    });
                    // console.log(marker);
                    marker.addListener('click', function () {
                        var id = infowincontent.id;
                        infoWindow.setContent(infowincontent);
                        infoWindow.open(map, marker);
                    });
                });
            });
            console.log("Finish initMap()");
            }
        function MoveToNowLocation(){
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {

                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                map.setCenter(pos);
                
            }, function () {
                handleLocationError(true, infoWindow, map.getCenter());
            });
           
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
        }
        }
        function doNothing() { }
        function updateMap(lat,lng){
                console.log("updateMap");
                var myLatLng = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                map.setCenter(myLatLng);
                map.setZoom(13);
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCw76om-jLHfTJAopu8jJtG2sIHve8Djrw&callback=initMap">
    </script>
</div>
</body>

</html>