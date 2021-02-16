function get_chart_data(item,month = $(".dash-m").val()){
  	$.ajax({
  		url:base_url+'chart_data',
  		type:"POST",
  		data:{type:item,month:month},
  		complete:function(){
  			loading("infin",false);	
  		},
  		success:function(response){
  			if (response.status) {
	  			if (item == 'income' || item == 'expenses') {
	  				var d = [];
	  				d[0] = ['Category','Amount'];
	  				for (var i = 0; i < response.m.length; i++) {
	  					d.push([response.m[i].name,response.m[i].totals]);
	  				}
	  				div = "."+item+"_total";		
	  				if (item == 'income') {
	  					$("."+item+"-tots").html(response.income_total)
	  				}else{
	  					$("."+item+"-tots").html(response.expenses_total)
	  				}
	  				create_chart(d,item+"_chart");			  			
	  			}else if(item == 'all'){

	  			}else if(item == 'accounts'){

	  			}
	  		}else{
  				alert(response.m);
  			}
  		},
  		error:function(){
  			alert("Internet error. Try again.");
  		}
  	})
  }
  $(".dash-m").each(function(){
  	$(this).click(function(){
  		var item = $(this).parent().parent().parent().attr("dash-item");
  		var month = $(this).val();
  		get_chart_data(item,month);
  	})
  })
  obj_to_array = function(obj){
  	obj = JSON.stringify(obj);
  	var res = Object.keys(obj).map(function(key){
  		return [Number(key),obj[key]];
  	});  	
  }
  create_chart = function(d = [],div = 'default'){      		  	  
      google.charts.setOnLoadCallback(drawChart);	      

      function drawChart() {

        var data = google.visualization.arrayToDataTable(d);

        var options = {
          title: '',
          is3D: true
        };

        if (div == 'default') {
        	var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        }else{        	
        	var chart = new google.visualization.PieChart(document.getElementById(div));
        }

        chart.draw(data, options);
      }
  }