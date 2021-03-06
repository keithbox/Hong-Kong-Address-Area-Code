<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Check Address Area</title>
<link href="../css/formElement.css" rel="stylesheet">
<link href="../css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<?php

if(isset($_POST['json'])){
	
$fileName = "check-address-area.txt";
//$file = fopen($fn, "a+");
//$size = filesize($fn);
	//fwrite($file, $_POST['json']);

//$text = fread($file, $size);
//fclose($file);
file_put_contents($fileName, $_POST['json']);
}
?>
<script src="../js/jquery-2.0.3.min.js"></script>
<script src="../js/jquery-ui-1.10.3.custom.js"></script>
<script>
var obj;
var jsonTextFile = "check-address-area.txt";

function ClearJSONTable(){
	document.getElementById('K').value = ''
	document.getElementById('H').value = ''
	document.getElementById('N').value = ''
	document.getElementById('F').value = ''
}
function GetJsonFile(){
		var jqxhr = $.get( jsonTextFile, function(data) {
		  $("pre.source").text(data);
		})
		.done(function() {
			$(".accordion").accordion("refresh");
		})
		  .fail(function() {
			alert( "get "+jsonTextFile+" fail." );
		  })
		  .always(function(){
			obj = JSON.parse($("pre.source").text());
			ExtractJson2Table();
		  })
}
function ExtractJson2Table(){
	$(function(){
			for(var key in obj.k){
				if( $("#K").val() == ""){
					$("#K").val(obj.k[key]);
				}else{
					$("#K").val($("#K").val()+", "+obj.k[key]);
				}
			}
			
			for(var key in obj.h){
				if( $("#H").val() == ""){
					$("#H").val(obj.h[key]);
				}else{
					$("#H").val($("#H").val()+", "+obj.h[key]);
				}
			}
			
			for(var key in obj.n){
				if( $("#N").val() == ""){
					$("#N").val(obj.n[key]);
				}else{
					$("#N").val($("#N").val()+", "+obj.n[key]);
				}
			}
			
			for(var key in obj.f){
				if( $("#F").val() == ""){
					$("#F").val(obj.f[key]);
				}else{
					$("#F").val($("#F").val()+", "+obj.f[key]);
				}
			}
	});
}
	$(function(){
		//$(".accordion").accordion();
		$(".accordion").accordion({ active: false, collapsible: true});
		$( ".resizable" ).resizable();
				
		GetJsonFile();
		
        $("#client-address").keyup(function () {
            var form = $(this).parents(".formEntry");
			form = form.find("#client-address").val();
            aKeyhWasType(form);
        });
        $("#client-address").change(function () {
            var form = $(this).parents(".formEntry");
			form = form.find("#client-address").val();
            aKeyhWasType(form);
        });
        var timeoutId;
        // Autosave input box's to database during pause in typing?
        // http://stackoverflow.com/questions/19910843/autosave-input-boxs-to-database-during-pause-in-typing
        // http://www.w3schools.com/jsref/met_win_cleartimeout.asp
        function aKeyhWasType(form) {
            console.log(arguments.callee.name + " function start");
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function () {
                // Runs 1 second (1000 ms) after the last change
                getTheSubstring(form);
            }, 3000);
        }
		
		function CustomAreaCode(address, areaCode){
			address = address.toLowerCase();
			
			// find the address with same area
			$("#custom-area-code-td  .political-area").each(function(index, element) {
				var tempVal = $(element).val();
				tempVal = tempVal.toLowerCase();
				// same address found
				if( tempVal == address){
					// find the same areacode
					$(element).next(".area-list").children("option").each(function(index2, optionElement) {
						
                        tempAreaCode = $(optionElement).val();
						console.log(tempAreaCode);
						console.log(areaCode);
						// select the founded areacode
						if(areaCode == tempAreaCode){
							$(optionElement).prop("selected", true);
							$(element).next(".area-list").next(".area-code").val(areaCode);
						}else{
							$(optionElement).prop("selected", false);
						}
                    });
				}
			});
			
			// refresh area code
				$("#to-excel").val("");
				$("#custom-area-code-td .area-code").each(function(index, element) {
					var value = $(element).val();
					if($("#to-excel").val()==""){
						$("#to-excel").val(value);
					}else{
					$("#to-excel").val($("#to-excel").val()+"\n"+value);
					}
				});
		}
		
		function getTheSubstring(text){
			text = text.trim();
			$("#custom-area-code-td").text("");
			$("#address-substring").val("");
			
			textInLine = text.split("\n");
			for(i=0; i<textInLine.length; i++){
				commaIndex = textInLine[i].lastIndexOf(",");
				
				var addressLastLine = textInLine[i].substring(commaIndex+1, textInLine[i].length);
				addressLastLine = addressLastLine.trim();
				$("#address-substring").val($("#address-substring").val()+addressLastLine+"\n");
				
				//list = $(".area-list");
				foundCode = "";
				
				
				$.each(obj, function(key, value) {
					isFound = value.indexOf(addressLastLine.toLowerCase());
					if(isFound>=0){
						foundCode = key.toUpperCase();
						return false;
					}else{
						foundCode = "";
					}
				});
				
				var $copy = $($(".sample")[0]).clone();
				$copy.find(".political-area").val(addressLastLine);
				$copy.find(".area-code").val(foundCode);
				
				//if(foundCode != "")
				//$copy.find(".area-list option[value='"+foundCode+"']").prop("selected", true);
				$copy.show().appendTo("#custom-area-code-td");
				
				$("#custom-area-code-td div:last-child .area-list option[value='"+foundCode+"'").prop("selected", true);
				
				if(foundCode !=""){
					$copy.find(".political-area").prop("readonly", true).addClass("ui-state-disabled");
					$copy.find("select").prop("disabled", true).addClass("ui-state-disabled");
					$copy.find(".area-code").prop("readonly", true).addClass("ui-state-disabled");
				}
				/*
				copy = "<div><input type='text' value='"+text+"'><select style='margin-left: 50px;'>"+list.html()+"</select><input type='text' value='"+foundCode+"'></div>";
				$("#custom-area-code-td").append("<div class='auto-gen'>"+copy+"</div>");
				*/
			}
			
			$(".area-list").change(function(){
				
				$(this).next().val($(this).val());
			});
			
			
			$("#custom-area-code-td .area-code").change(function(e) {
				var address = $(this).prev(".political-area").val();
				var areaCode = $(this).val();
				/*
				$("#to-excel").val("");
				$("#custom-area-code-td .area-code").each(function(index, element) {
					var value = $(element).val();
					if($("#to-excel").val()==""){
						$("#to-excel").val(value);
					}else{
					$("#to-excel").val($("#to-excel").val()+"\n"+value);
					}
				});
				*/
				CustomAreaCode(address, areaCode);
			});
			
			$("#custom-area-code-td .area-list").change(function(e) {
				var address = $(this).prev(".political-area").val();
				var areaCode = $(this).val();
				/*
				$("#to-excel").val("");
				$("#custom-area-code-td .area-code").each(function(index, element) {
					var value = $(element).val();
					if($("#to-excel").val()==""){
						$("#to-excel").val(value);
					}else{
					$("#to-excel").val($("#to-excel").val()+"\n"+value);
					}
				});
				*/
				CustomAreaCode(address, areaCode);
			});
			
			$("#to-excel").val("")
			$("#custom-area-code-td .area-code").each(function(index, element) {
					var value = $(element).val();
					if($("#to-excel").val()==""){
						$("#to-excel").val(value);
					}else{
						
						$("#to-excel").val($("#to-excel").val()+"\n"+value);
						/*
						if(value==""){
						$("#to-excel").val($("#to-excel").val()+"\n\n");
						}else{
							
						$("#to-excel").val($("#to-excel").val()+"\n"+value);
						}
						*/
					}
			});
		
		}
		
		$(".update-dictionary").click(function(e) {
            $(this).prop("disabled", true);
			
			// is definition need to update
			$(".updateMe").each(function(index, element) {
                $(element).val("");
            });
			
			//$(".area-code:not([readonly]):not(:empty)").each(function(index, element) {
			$("#custom-area-code-td .area-code:not([readonly])").each(function(index, element) {
                var newArea = $(element).prevAll(".political-area").val();
				var newAreaCode = $(element).val();
				
				// skip which option are not choosed
				if(newAreaCode == "" || newArea == "")
					return true;
				
				// get the pre update dictionary container
				var dictionary = $("#updateMe-"+newAreaCode).val();
				if(dictionary == ""){
					$("#updateMe-"+newAreaCode).val(newArea);
				}else{
					if(dictionary.toLowerCase().indexOf(newArea.toLowerCase()) >= 0 ){
						return true;
					}else{
						$("#updateMe-"+newAreaCode).val($("#updateMe-"+newAreaCode).val()+", "+newArea);
					}
				}
            });
            $(this).prop("disabled", false);
        });
		
		$(".confirm-update").click(function(e) {
			isUpdate = true;
			$(".updateMe").each(function(index, element) {
                if($(element).val() == ""){
					isUpdate = isUpdate && true;
				}else{
					isUpdate = false;
				}
            });
			
			if(isUpdate){
            	$(this).prop("disabled", false);
				return false;
			}
			
			
			$.each(obj, function(key, value) {
				$(".updateMe").each(function(index, element) {
					if($(element).hasClass(key)){
						if($(element).val()==""){
							return false;
						}
						var textList = $(element).val().split(",");
						for(var index in textList){
							text = textList[index];
							text = text.trim();
							obj[key].push(text.toLowerCase());
						}
					}
				});
			});
			
			$(".new-source").val(JSON.stringify(obj, undefined, 2));
            
        });
	});
</script>
<style>
body {
	font-size: 12px;
}
div.sample {
	border: 1px solid #999;
	padding: 10px;
	margin-top: 5px;
	margin-bottom: 10px;
	background-image: -moz-linear-gradient(top, #FFFFFF, #F2F2F2);
	background-image: -ms-linear-gradient(top, #FFFFFF, #F2F2F2);
	background-image: -o-linear-gradient(top, #FFFFFF, #F2F2F2);
	background-image: -webkit-linear-gradient(top, #FFFFFF, #F2F2F2);
	background-image: linear-gradient(top, #FFFFFF, #F2F2F2);
	border-radius: 10px;
}
div.sample input, div.sample textarea, div.sample select {
	padding: 5px;
	border: 1px solid #CCC;
}
</style>
</head>
<body>

	  <div class="accordion">
	    <h3>Source code</h3>
	    <div id="sourceCode">
          <pre class="source">{
	"h":[
		"southern",
		"central & western",
		"central and western",
		"wan chai",
		"eastern"
	],
	"f":[
		"island",
		"lantau island",
		"lantao island",
		"cheung chau"
	],
	"k":[
		"sham shui po",
		"kowloon city",
		"kwun tong",
		"wong tai sin",
		"yau tsim mong"
	],
	"n":[
		"island",
		"kwai tsing",
		"north",
		"sai kung",
		"sha tin",
		"shatin",
		"tai po",
		"tsuen wan",
		"tuen mun",
		"yuen long"
	]
}</pre>
        </div>
        </div>
	  <div class="accordion">
	    <h3>Current Dictionary</h3>
	    <div>
      <div>Kowloon: <textarea class="searchMe" id="K" cols="100" rows="3"></textarea></div>
      <div>Hong Kong: <textarea class="searchMe" id="H" cols="100" rows="3"></textarea></div>
      <div>New Territories: <textarea class="searchMe" id="N" cols="100" rows="3"></textarea></div>
      <div>Foreign: <textarea class="searchMe" id="F" cols="100" rows="3"></textarea></div>
      </div>
	  </div>
      <hr>
      <h2>How to use</h2>
      <ol>
        <li>
          Make sure the current directory matach table are work.</li>
        <li>Place your list of address copy from excel to the left panel at below and wait</li>
        <li>The sentence trim by , will display at the right panel, at the same time..</li>
        <li>The program will try to identify  the area code of each line of address</li>
        <li>You may assign  area code in the custom address area code panel manually</li>
      </ol>
      <h2>How to update the dictionary</h2>
      <ol>
        <li>After finish you maual assignment, click the [update dictionary] button</li>
        <li>Your new keywords will display at the &quot;Dictionary New Definition&quot; panel</li>
        <li>You may change and edit in the &quot;Dictionary New Definition&quot; panel</li>
        <li>Click [Generate New JSON] button, the new json code will include the new dictionary definition</li>
        <li>Click [submit] to update the json file by PHP or copy the json code update by yourself</li>
</ol>
      <h2>Version 2014-08-29</h2>
      <ol>
        <li>Require web server to get JSON file</li>
        <li>Require PHP server to update the JSON file</li>
</ol>
          <hr>
      <table class="resizable" width="1080" border="0" cellspacing="5" cellpadding="0" style="border: 1px solid black;">
  <tr>
    <td width="640"><h3 class="ui-widget-header">Press your address here</h3><form class="formEntry">
    <textarea name="client-address" id="client-address" cols="100" rows="10"></textarea></form></td>
    <td width="425"><h3 class="ui-widget-header">Get the substring</h3>
    <textarea name="address-substring" id="address-substring" cols="50" rows="10" readonly></textarea></td>
  </tr>
</table>
      <hr>
      <div class="accordion">
	    <h3>Dictionary New Definition</h3>
        <div>
      <div><label>Kowloon:</label> <textarea class="updateMe k" id="updateMe-K" cols="100" rows="3"></textarea></div>
      <div><label>Hong Kong:</label> <textarea class="updateMe h" id="updateMe-H" cols="100" rows="3"></textarea></div>
      <div><label>New Territories:</label> <textarea class="updateMe n" id="updateMe-N" cols="100" rows="3"></textarea></div>
      <div><label>Foreign:</label> <textarea class="updateMe f" id="updateMe-F" cols="100" rows="3"></textarea></div>
      </div>
	  </div>
	  <div class="accordion">
	    <h3>New Json</h3>
      <div><form action="#" method="post">
      <textarea class="new-source" name="json" rows="10" cols="100"></textarea>
<button type="submit">submit</button></form></div>
</div>
      <button class="update-dictionary">Update Dictionary</button>
      <button class="confirm-update">Generate New JSON</button>
      <hr>
<div class="sample" style="margin-left: 50px; display: none;">
    <input type="text" class="political-area">
    <select class="area-list" style="margin-left: 50px;">
      <option value="" selected>--Select--</option>
      <option value="K">Kowloon</option>
      <option value="H">Hong Kong</option>
      <option value="N">New Territories</option>
      <option value="F">Foreign</option>
    </select>
    <input type="text" class="area-code" style="margin-left: 50px; text-transform: uppercase;"></div>
      <table width="1080" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid black;">
  <tr>
    <td width="640" style="vertical-align: text-top;"><h3>Custom address area code</h3>
    <div id="custom-area-code-td"></div></td>
    <td width="438" style="vertical-align: text-top;"><h3>Area code identification</h3>
    <textarea cols="40" rows="20" id="to-excel" style="margin-left: 50px;"></textarea></td>
  </tr>
</table>
</body>
</html>
