
	$(document).ready(function(){
		$("#toggle-login").click(function(){
			$(this).parent().hide();
			$(".register-form").show();	
		});
		$("#toggle-register").click(function(){
			$(this).parent().hide();	
			$(".login-form").show();
		});
		// hide parent of anything with id=hide 
		$("#hide").click(function(){
			$(this).parent().parent().slideUp('fast');
		});
		// show id=connect 
		$("#connect").click(function(){
			$(".connect-form").show();		
		});
		// hide parent of anything with id=hide 
		$("#connecthide").click(function(){
			$(this).parent().slideUp('fast');
		});
		
		
		// START -- calculate minutes since set off
		var nowDate = $('#time-left').html(); 
		var startDate = new Date(nowDate);
		setInterval(function(){getTime(startDate);}, 1000);
		$('#time-left').html(startDate);
		
		function getTime(startDate){   
			var date = new Date();
		  	var seconds = (date - startDate)/1000;
		  	$('#time-left').html(toHHMMSS(seconds));
		}

		var nowDate2 = $('#time-left-2').html(); 
		var startDate2 = new Date(nowDate2);
		setInterval(function(){getTime2(startDate2);}, 1000);
		$('#time-left-2').html(startDate); 

		function getTime2(startDate2){   
			var date = new Date();
		  	var seconds = (date - startDate2)/1000;
		  	$('#time-left-2').html(toHHMMSS(seconds));
		}	

		function toHHMMSS(sec) {
			var sec_num = parseInt(sec, 10);
			var hours   = Math.floor(sec_num / 3600);
			var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
			var seconds = sec_num - (hours * 3600) - (minutes * 60);

			if (hours   < 10) {hours   = "0"+hours;}
			if (minutes < 10) {minutes = minutes;}
			if (seconds < 10) {seconds = "0"+seconds;}
		  	//  var time    = hours+':'+minutes+':'+seconds;
	  		if(hours > 0) { var time = hours * 60 + minutes; }
	  		if(hours == 0) { var time = minutes; }
		  	
			return time;
		}});
		// END -- calculate minutes since set off
		
		
		// START -- calculate distance
		
		//var y = document.getElementById("jqd");
		
		//var y = $("#jqd").text();
		//alert(y);
		//var y = document.getElementById("jqd"); // original
		//alert(y);
		//var myCoords = y.split(",");
		alert(myCoords[0] + "\n" + myCoords[1]);
		
		
		function getLocation() {
		  	if (navigator.geolocation) {
				navigator.geolocation.watchPosition(showPosition);
			}else{y.innerHTML="Geolocation is not supported by this browser.";}
		}
		
		//duplicate for first time position
		var z = document.getElementById("FTP");
		function getLocation() {
		  	if (navigator.geolocation) {
				navigator.geolocation.watchPosition(showPosition);
			}else{z.innerHTML="Geolocation is not supported by this browser.";}
		}
	  
		function distance(lat1, lng1, lat2, lng2) {
			var miles = true;
			var pi80 = Math.PI / 180;
			var lat1 = lat1 * pi80;
			var lng1 = lng1 * pi80;
			var lat2 = lat2 * pi80;
			var lng2 = lng2 * pi80;
		
	 
			var r = 6372.797; // mean radius of Earth in km
			console.log("r:"+r);
			var dlat = lat2 - lat1;
			var dlng = lng2 - lng1;
			var a = Math.sin(dlat / 2) * Math.sin(dlat / 2) + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dlng / 2) * Math.sin(dlng / 2);
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
			var km = r * c;
	 
			var dist = (miles ? (km * 0.621371192) : km);
			return dist.toFixed(1);
		}
	  
	  
		function showPosition(position) {
		  	y.innerHTML="Latitude: " + position.coords.latitude + 
		  	"<br>Longitude: " + position.coords.longitude;	
	  		
		  	var lati = position.coords.latitude;
		  	var lng = position.coords.longitude;
		
			$("#sethome").text("<a href='?lat="+lati+"&lng"+lng+"'>Set Home</a>");
		  	
		  	var setHome = "<a id='setHomeLink' href='postlandr.php?action=set-home&lat=" + lati + "&lng=" + lng + "'>Set home</a>";
	  		$("#linkToSetHome").html(setHome);
	  		
		  	//var stuff = distance("<?php echo $latHome; ?>","<?php echo $lngHome; ?>",lati,lng); // original
			var stuff = distance(myCoords[0],myCoords[1],lati,lng); //trying explode
		  	$("#jqd").text(stuff);
		}
		
	    $("#locationNow").click(function(){
	        $("#demo").addClass("blue");
	        getLocation();
	    });	
	    
	    $('#jqd').append(getLocation());
		
		$(document).on("click","#setHomeLink",function(){
			var r = confirm("Reset home?");
			if (r==true) {
				x="Home reset.";
			} else {
				x="You pressed Cancel!";
				return false;
			}
			alert(x);
			
		});
		
		// END -- calculate distance
	  	

