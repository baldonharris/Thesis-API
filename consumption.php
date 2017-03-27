<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Consumptions</title>
		<link href="consumptions/css/bootstrap.min.css" rel="stylesheet">
		<link href="consumptions/css/morris.css" rel="stylesheet">
		<link href="consumptions/css/jquery-ui.css" rel="stylesheet">
		<link href="consumptions/css/monthpicker.css" rel="stylesheet">
	</head>
	<body style="background-color: rgb(0,178,252); font-size: 13px;">
		<div class="container">
			<div class="row">
				<div class="col-xs-3">
					<table>
						<tr>
							<td><label for="perView">View by:</label></td>
							<td>
								<select id="perView" style="width: 100px;" value="1">
									<option value="1">Hour</option>
									<option value="2">Day</option>
								</select>
							</td>
						</tr>
						<tr class="forHour">
							<td><label for="perView">Date:</label></td>
							<td><input type="text" id="datepicker" style="width: 100px;"/></td>
						</tr>
						<tr class="forDay" style="display: none;">
							<td><label for="perView">From:</label></td>
							<td><input type="text" id="date_start" class="from" style="width: 100px;"/></td>
						</tr>
						<tr class="forDay" style="display: none;">
							<td><label for="perView">To:</label></td>
							<td><input type="text" id="date_end" class="to" style="width: 100px;"/></td>
						</tr>
						<tr class="forWeek" style="display: none;">
							<td><label for="perView">From:</label></td>
							<td><input type="text" id="weekpickerfrom" class="from" style="width: 100px;"/></td>
						</tr>
						<tr class="forWeek" style="display: none;">
							<td><label for="perView">To:</label></td>
							<td><input type="text" id="weekpickerto" class="to" style="width: 100px;"/></td>
						</tr>
						<tr class="forMonth" style="display: none;">
							<td><label for="perView">From:</label></td>
							<td><input type="text" class="monthpicker" style="width: 100px;"/></td>
						</tr>
						<tr class="forMonth" style="display: none;">
							<td><label for="perView">To:</label></td>
							<td><input type="text" class="monthpicker" style="width: 100px;"/></td>
						</tr>
						<tr>
							<td><input id="urlRequest" type="hidden" value="index.php"/></td>
							<td><button id="submitView">View</button></td>
						</tr>
						<tr>
							<td><input id="urlRequest" type="hidden" value="index.php"/></td>
							<td><br><br><br><br><br><br><br><br><br><br><br><br><a style="color:#fff;" id="open_table" href="#">Open Table</a></td>
						</tr>
					</table>
				</div> <!-- end col-xs-2 -->
				<div class="col-xs-9" style="color:#fff">
					<div id="displayMe" style="height: 250px; width: 530px;">
						<div id="myfirsttable" style="display:none;">
							<center>Power consumption of all the rooms</center>
							<table class="table table-condensed">
								<tr><td><strong>Time</strong></td><td><strong>Consumption</strong></td></tr>
								<tbody id="tableDisplay"></tbody>
							</table>
						</div>
						<b class="label labelMe" style="display:none;">Consumption</b><div style="height: 300px;" id="myfirstchart"></div><br><center><b class="label labelMe" style="display:none;">Time</b></center>
					</div>
				</div>
			</div> <!-- end row -->
		</div> <!-- end container -->
		<script src="consumptions/js/jquery.js"></script>
		<script src="consumptions/js/jquery-ui.min.js"></script>
		<script src="consumptions/js/weekpickerfrom.js"></script>
		<script src="consumptions/js/weekpickerto.js"></script>
		<script src="consumptions/js/monthpicker.js"></script>
		<script src="consumptions/js/raphael.js"></script>
		<script src="consumptions/js/myjs.js"></script>
		<script src="consumptions/js/morris.js"></script>
		<script>
			$(document).ready(function(){
				var dataTable;
				var flag=0;
				var pView;

				$('#open_table').click(function(){
					if(flag==0){
						if(!dataTable){
							return;
						}else{
							console.log(dataTable);
							$('#tableDisplay').empty();
							if(pView == 1){
								for(x=0; x<dataTable.length; x++){
									$('#tableDisplay').append('<tr><td>'+dataTable[x].hour+'</td><td>'+dataTable[x].cons+'</td></tr>');
								}
							}else{
								for(x=0; x<dataTable.length; x++){
									$('#tableDisplay').append('<tr><td>'+dataTable[x].date+'</td><td>'+dataTable[x].cons+'</td></tr>');
								}
							}
							
							$('#open_table').text("Open Graph");
							$('#myfirstchart').hide();
							$('#myfirsttable').show();
							$('.labelMe').hide();
							flag=1;
						}
					}else{
						$('#open_table').text("Open Table");
						$('#myfirstchart').show();
						$('#myfirsttable').hide();
						$('.labelMe').show();
						flag=0;
					}
				});

				$('#submitView').click(function(){
					$('#myfirstchart').empty();

					var url = $('#urlRequest').val();
					var perView = $('#perView').val();
					var date_me = $('#datepicker').val();
					var date_start = $('#date_start').val();
					var date_end = $('#date_end').val();
					var date_sweek = $('#weekpickerfrom').val();
					var date_eweek = $('#weekpickerto').val();

					$('.label').show();
					pView = perView;

					var data_to_pass = {
						function: 'get_consumption_for_graph',
						per_view: perView,
						date_me: date_me,
						date_start: date_start,
						date_end: date_end,
						date_sweek: date_sweek,
						date_eweek: date_eweek
					};
					
					$.get(url, data_to_pass, function(data){
						dataTable = data.data;
						if(perView == 1){
							new Morris.Line({
								element: 'myfirstchart',
								parseTime: false,
								data: data.data,
								xkey: 'hour',
								ykeys: ['cons'],
								xLabelMargin: 10,
								xLabelAngle: 45,
								postUnits: 'kWhr',
								smooth: 'false',
								labels: ['Consumption'],
								gridTextColor: ['#ffffff'],
								eventLineColors: ['#ffffff'],
								goalLineColors: ['#ffffff'],
								axes: true,
								grid: true
							});
						}else if(perView == 2){
							new Morris.Line({
								element: 'myfirstchart',
								data: data.data,
								xLabels: 'day',
								xLabelMargin: 1,
								xkey: 'date',
								ykeys: ['cons'],
								postUnits: 'kWhr',
								labels: ['Consumption'],
								gridTextColor: ['#ffffff'],
								eventLineColors: ['#ffffff'],
								goalLineColors: ['#ffffff'],
								xLabelAngle: 45,
								smooth: 'false',
								xLabelFormat: function (x) {
									var IndexToMonth = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
									var month = IndexToMonth[ x.getMonth() ];
									var day = x.getDate();
									return month + ' ' + day;
								}
							});
						}
					});
				});
			});
		</script>
	</body>
</html>