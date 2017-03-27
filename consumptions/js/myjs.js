$(document).ready(function(){
	var select = 1;
	$('#datepicker').datepicker();
	$('.monthpicker').MonthPicker({Button: false});

	$( ".from" ).datepicker({
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	
	$( ".to" ).datepicker({
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$( "#from" ).datepicker( "option", "maxDate", selectedDate );
		}
	});

	$('#perView').change(function(){
		select = $(this).val();
		var hour = $('.forHour').hasClass('hidden');
		var day = $('.forDay').hasClass('hidden');
		var week = $('.forWeek').hasClass('hidden');
		var month = $('.forMonth').hasClass('hidden');
		if(select == 1){
			$('.forHour').show();
			$('.forDay').hide();
			$('.forWeek').hide();
			$('.forMonth').hide();
		}else if(select == 2){
			$('.forHour').hide();
			$('.forDay').show();
			$('.forWeek').hide();
			$('.forMonth').hide();
		}else if(select == 3){
			$('.forHour').hide();
			$('.forDay').hide();
			$('.forWeek').show();
			$('.forMonth').hide();
		}else{
			$('.forHour').hide();
			$('.forDay').hide();
			$('.forWeek').hide();
			$('.forMonth').show();
		}
	});
});