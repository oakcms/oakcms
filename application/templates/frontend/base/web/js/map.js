/*map*/

$(window).on('load', function() {
    // Create a map object and specify the DOM element for display.
    var map1 = new google.maps.Map(document.getElementById('map1'), {
        center: {lat:50.6156207, lng: 26.2645903},
        scrollwheel: false,
        zoom: 15
    });
    //var image = 'img/icons/point_map.png';
    var marker1 = new google.maps.Marker({
        position: {lat: 50.6156207, lng: 26.2645903},
        map: map1
        //icon: image
    });
    google.maps.event.addListener(marker1, 'click', function() {
        infowindow1.open(map1,marker1);
    });

    var infowindow1 = new google.maps.InfoWindow({
        content: 'Украина, г.Ровно, ул.Княгини Ольги, 5, оф. 314',
        maxWidth: 300
    });


    var map2 = new google.maps.Map(document.getElementById('map2'), {
        center: {lat:50.2618239, lng: 19.0121023},
        scrollwheel: false,
        zoom: 14
    });
    //var image = 'img/icons/point_map.png';
    var marker2 = new google.maps.Marker({
        position: {lat: 50.2618239, lng: 19.0121023},
        map: map2
        //icon: image
    });
    google.maps.event.addListener(marker2, 'click', function() {
        infowindow2.open(map2,marker2);
    });

    var infowindow2 = new google.maps.InfoWindow({
        content: 'Poland Katowice, ul.Sobieskiego, 11',
        maxWidth: 300
    });


   
});