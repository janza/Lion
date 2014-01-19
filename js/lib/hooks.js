// hooks
function initHooks() {
	var jsdoc = $(document);
	jsdoc.on('click','.sensor-unit-link', toggleSensorUnit);
	jsdoc.on('click', '.sensor-name', function() {
		$(this).toggleClass('collapsed');
	});
	jsdoc.on('change','#startDate,#startTime,#endDate,#endTime', function(){
		startTime = new Date($('#startTime').val() + ' ' + $('#startDate').val());
		endTime = new Date($('#endTime').val() + ' ' + $('#endDate').val());
		if (activeUnits.length) {
			getSensorData();
		}
	});

	$('#startDate,#endDate').datepicker();
	$('#startTime,#endTime').timepicker();
}