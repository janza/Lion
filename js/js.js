(function($) {
	var compiled = {};
	$.fn.handlebars = function(template, data) {
		if (template instanceof jQuery) {
			template = $(template).html();
		}

		compiled[template] = Handlebars.compile(template);
		this.html(compiled[template](data));
	};
})(jQuery);

// hooks
function initHooks() {
	var jsdoc = $(document);
	jsdoc.on('click','.sensor-unit-link', toggleSensorUnit);
	jsdoc.on('click', '.sensor-name', function() {
		$(this).toggleClass('collapsed');
		return false;
	});
	jsdoc.on('change','#startDate,#startTime,#endDate,#endTime', function(){
        var stime = dateFormatNice($('#startDate').datepicker('getDate')) + ' ' + $('#startTime').val();
        var etime = dateFormatNice($('#endDate').datepicker('getDate')) + ' ' + $('#endTime').val();

        startTime = new Date(stime);
        endTime = new Date(etime);

        startTime = setTimeOffset(startTime);
        endTime = setTimeOffset(endTime);

		if (activeUnits.length) {
			getSensorData();
		}
	});


	$('#startDate,#endDate').datepicker({ dateFormat: "yy-mm-dd", maxDate: "0" });
	$('#startDate').datepicker('setDate','-1');
    $('#endDate').datepicker('setDate','0');
    $('#startTime,#endTime').timepicker().timepicker('setTime',timeFormat(endTime));

	$('#limitSubmit').click(function() {
		getSensorData(true);
		return false;
	});

}


function bindGraphEvents() {

	/* graph select event */
    $('.graph').bind('plotselected', function(event, ranges) {
        startTime = new Date(ranges.xaxis.from);
        endTime = new Date(ranges.xaxis.to);
        setTimeFields();
        if (activeUnits.length) {
            getSensorData();
        }
    });

    /* graph hover event */
    var previousPoint = null;
    $('.graph').bind("plothover", function (event, pos, item) {

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;

                $("#tooltip").remove();
                var x = item.datapoint[0],
                    y = item.datapoint[1].toFixed(2);

                showTooltip(item.pageX, item.pageY,
                    item.series.label + ' on ' + dateFormatNice(new Date(x-1000*60*60*2)) + '@' + timeFormat(new Date(x-1000*60*60*2)) + ' = <strong>' + y + '</strong>');
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;
        }

    });

}

var appPath = document.location.href.match(/http(.)+php/)[0],
    apiUrl = appPath+'/api/',
    plot,
    sensors = [],
    units = {},
    endTime = new Date();
    startTime = new Date(endTime - 1000*60*60*24);

var activeUnits = [];

var plotOptions = {
    series: {
        lines: {
            show: true
        },
        points: {
            show: true
        },
        bars: {
            show: false
        }
    },
    crosshair: {
        mode: "x"
    },
    grid: {
        hoverable: true
    },
    xaxis: {
        mode: "time"
    },
    yaxis: {
        min: 10,
        max: 30
    },
    selection: {
        mode: "x"
    }
};


function toggleSensorUnit() {
    var $this = $(this);
    var unitLabel = $this.find('.label');
    var unitName = $this.find('.unit-name').text();
    var unitID = $this.data('unit-id');
    var sensorID = $this.data('sensor-id');
    var sensorName = $this.data('sensor-name');
    var uniqueID = getUniqueID(sensorID, unitID);
    var toggled = unitLabel.toggleClass('label-success label-default').hasClass('label-success');


    if ( toggled ) {
        unitLabel.text('On');
    } else {
        unitLabel.text('Off');
    }

    for ( var i in activeUnits ) {
        if ( ( activeUnits[i]['unit_id'] === unitID ) &&
             ( activeUnits[i]['sensor_id'] === sensorID ) )
        {
            activeUnits.splice(i,1);
        }
    }

    if (toggled) {
        activeUnits.push({
            'unit_id': unitID,
            'sensor_id': sensorID,
            'unit_name': unitName,
            'sensor_name': sensorName
        });
    }

    if (activeUnits.length) {
        getSensorData();
    }

    $('#unitSelect').handlebars( $('#valueTmpl'), { 'units': activeUnits } );

    return false;

}





function getSensorData(getByVal) {

    var dayDiff = getDayDiff( startTime, endTime );
    var dayPartDiff = getDayPartDiff( startTime, endTime );
    var hourDiff = getHourDiff( startTime, endTime );
    var method = 'LReadings';
    var units = activeUnits;

    var dataArray = {};

    plotOptions.series.lines.show = true;
    plotOptions.series.bars.show = false;

    if ( dayDiff > 200 ) {
        method = 'AggregateMonth';
    } else if ( dayPartDiff > 100 ) {
        method = 'AggregateDay';
        dataArray = getDay();
    } else if ( hourDiff > 24 ) {
        method = 'AggregateDayPart';
        dataArray = getDayPart();
    } else {
        method = 'LReadings';
        dataArray = getLReading();
    }

    dataArray.units = units;

    if (getByVal) {
        method = 'AggregateDay';
        dataArray.limit = $('#limitText').val();
        dataArray.compare = $('#limitSelect').val();
        dataArray['limit_unit_id'] = $('#unitSelect option:selected').data('unit-id');
        dataArray['limit_sensor_id'] = $('#unitSelect option:selected').data('sensor-id');
        plotOptions.series.lines.show = false;
        plotOptions.series.bars.show = true;
    }


    drawData( method, dataArray );

}

/*
 * LReading
*/

function getLReading(units) {
    var dataArray = {};

    dataArray.startTime = startTime.getHours()*60*60+startTime.getMinutes()*60;
    dataArray.endTime = endTime.getHours()*60*60+endTime.getMinutes()*60;
    dataArray.startDate = dateFormat(startTime);
    dataArray.endDate = dateFormat(endTime);

    return dataArray;

}

function formatLReadings( date, time ) {
    date = date + '';
    date = date.slice(0,4) + '/' + date.slice(4,6) + '/' + date.slice(6,8);
    return [ new Date(date).valueOf() + time * 1000, false ];
}


/*
 * DayPart
*/


function getDayPart(units) {
    var dataArray = {};

    dataArray.startPart = Math.floor(startTime.getHours()/5)+1;
    dataArray.endPart = Math.floor(endTime.getHours()/5)+1;
    dataArray.startDate = dateFormat(startTime);
    dataArray.endDate = dateFormat(endTime);

    return dataArray;

}

function formatDayPart( date, time, prevData ) {
    var missing = false;
    var prevTime= prevData[1];
    var prevDate= prevData[0];


    var dayDiff = Math.abs( date - prevDate );
    var dayPartDiff = Math.abs( time - prevTime );

    if ( ( dayDiff > 1 ) && ( dayDiff < 60 ) || ( dayPartDiff > 1 ) && ( dayDiff != 1 ) ) {
        missing = true;
    }

    time = dayPartFormat(time);

    date = date + '';
    date = date.slice(0,4) + '/' + date.slice(4,6) + '/' + date.slice(6,8);

    return [ new Date(date).valueOf() + time , missing];
}

/*
 * Days
*/

function getDay(units) {
    var dataArray = {};

    dataArray.startDate = dateFormat(startTime);
    dataArray.endDate = dateFormat(endTime);
    dataArray.units = units;

    return dataArray;

}

function formatDay( date, time, prevData ) {

    var prevDate = prevData[0];

    var missing = false;
    if ( (Math.abs( date - prevDate ) > 1) && (Math.abs( date - prevDate ) < 60) ) {
            missing = true;
    }

    date = date+'';
    date = date.slice(0,4) + '/' + date.slice(4,6) + '/' + date.slice(6,8);

    return [ new Date(date).valueOf(), missing ];
}

/**
 * Format the data for showing on graph (show the right time and date).
 *
 * @param {array} data - Data received from backend through REST API
 *
 * @returns {array} - Formated data
 */

function formatData( data, table ){
    var tempData = [];
    var outData = [];
    var min = 10;
    var max = 30;

    if ( table === 'LReadings' ) {
        formatDate = formatLReadings;
    } else if ( table === 'AggregateDayPart' ) {
        formatDate = formatDayPart;
    } else if ( table === 'AggregateDay' ) {
        formatDate = formatDay;
    }

    // initiate each unit info
    for ( var i in activeUnits ) {
        var elem = activeUnits[i];
        var uniqueID = getUniqueID( elem.sensor_id, elem.unit_id );

        tempData[uniqueID] = {
            label: sensors[elem.sensor_id],
            data:[],
            hoverable: true,
            unit_id: elem.unit_id,
            unit_name: elem.unit_name
        };
    }

    // iterating over each reading
    $.each(data, function(i,elem){
        var uniqueID = getUniqueID( elem.sensor_id, elem.unit_id ),
            newPoint = [], timeinfo, time,
            value = (elem.value || elem.avg_value || elem.max_value || elem.min_value) * 1 ,
            data = [elem.date_id, (elem.time_id || elem.day_part_id)],
            prevData = data, missing;

        if (tempData[uniqueID].data.length) {
            prevData = tempData[uniqueID].data.slice(-1)[0][2];
        } else {
            tempData[uniqueID].data.push([startTime*1, null, null]);
        }

        timeinfo = formatDate( data[0], data[1], prevData );
        time = timeinfo[0];
        missing = timeinfo[1];

        if (missing) {
            tempData[uniqueID].data.push([ time+1, null ]);
        }

        if ( value > max ) {
            max = value;
        } else if ( value < min ) {
            min = value;
        }

        newPoint = [time, value, [elem.date_id, (elem.time_id || elem.day_part_id)]];

        tempData[uniqueID].data.push(newPoint);

    });

    plotOptions.yaxis.min = min - 3;
    plotOptions.yaxis.max = max + 3;

    // return data
    for( i in tempData ) {
        tempData[i].data.push([endTime*1, null, null]);
        outData[tempData[i].unit_id] = (outData[tempData[i].unit_id] || []);
        outData[tempData[i].unit_id].push(tempData[i]);
    }

    return outData;

}


/*
* Drawing data !
*
*
*
*/

function drawData( method, dataArray ) {

    loading();

    getREST( method, dataArray, function(data) {
        var graphData = formatData(data.data,method);
        var graph = '';
        var $graphs = $('#graphs');
        var graphName = '';

        $graphs.html('');

        $.noty.clearQueue();
        $.noty.closeAll();

        for (var i in graphData) {
            graphName = graphData[i][0].unit_name;
            graphs = '<h3 class="graph-name">' + graphName + '</h3><div class="graph" id="graph' + i + '"/>';

            $graphs.append(graphs);
            plot = $.plot( $('#graph'+i), graphData[i], plotOptions );
        }

        bindGraphEvents();
    });

}



/* Get sensors */
function getSensors() {

    getREST( 'Sensors', function(data) {
        var gsnArray = {};
        var gsns = [];

        $.each( data.data, function(i,elem) {
            sensors[elem.sensor_id] = elem.sensor_user_name;

            gsnArray[elem.gsn_id.gsn_id] = ( gsnArray[elem.gsn_id.gsn_id] || { 'data': [] , 'name': elem.gsn_id.gsn_name, 'id': elem.gsn_id.gsn_id } );
            if ( elem.gsn_id.gsn_name && gsnArray[elem.gsn_id.gsn_id].name) {
                gsnArray[elem.gsn_id.gsn_id].name = elem.gsn_id.gsn_name;
            }
            gsnArray[elem.gsn_id.gsn_id].data.push(elem);

        });

        $.each( gsnArray, function(i,elem) {
            gsns.push(elem);
        });

        $('#senzori').handlebars( $('#sensorTmpl'), { 'gsn': gsns } );

        initTooltips();
    });

}


function init() {

    getSensors();

    initHooks();
}


$(init);

// funkcije koje dohvacaju podatke sa yii REST API-a

/**
 * List the specific yii model records.
 *
 * @param {string} model The yii model we are accessing.
 * @param {object} search Optional parameter used for searching the database,
 *                        if omited all records are listed.
 * @param {function} callback Callback function.
 *
 * @returns {null}
 */

function getREST(model, search, callback) {
	/*
	* zahtjev za islistavanjem
	*  svih zapisa u tablici
	*/
	if (isFunction(search)) {
		$.ajax(apiUrl + model + '/', {
			success: search,
			type: 'GET',
			error: generalError
		});
	} else {
		/*
		* zahtjev za podacima koji zadovoljavaju
		*  uvjete zadane u search parametru
		*/
		$.ajax(apiUrl + model + '/search', {
			success: callback,
			type: 'POST',
			data: JSON.stringify(search),
			processData: false,
			error: generalError
		});
	}
}

// tests


// utils

function getDate(date) {
	date = '' + date;
	return date.substr(0, 4) + '-' + date.substr(4, 2) + '-' + date.substr(6, 2);
}

function isFunction(functionToCheck) {
	var getType = {};
	return functionToCheck && getType.toString.call(functionToCheck) == '[object Function]';
}

function getUniqueID(sensor_id,unit_id) {
	return sensor_id*20+unit_id;
}

function getDayDiff(date1, date2) {
	return Math.abs(date2-date1)/1000/60/60/24;
}

function getDayPartDiff(date1, date2) {
	return Math.abs(date2-date1)/1000/60/60/5;
}

function getHourDiff(date1, date2) {
	return Math.abs(date2-date1)/1000/60/60;
}

function dateFormatISO(date) {
    return getDate(dateFormat(date));
}
function dateFormat(date) {
	return ''+date.getFullYear()+pad(date.getMonth()+1)+pad(date.getDate());
}

function dateFormatNice(date) {
	return ''+pad(date.getMonth()+1)+'/'+pad(date.getDate())+'/'+date.getFullYear();
}

function timeFormat(date) {
	return ''+pad(date.getHours())+':'+pad(date.getMinutes());
}

function setTimeOffset(time) {
    return new Date(time*1 - endTime.getTimezoneOffset()*60*1000);
}

function dayPartFormat(time) {
	if ( time == 1 ) {
        time = 7.5*60*60*1000;
    } else if ( time == 2 ) {
        time = 10.5*60*60*1000;
    } else if ( time == 3 ) {
        time = 14.5*60*60*1000;
    } else if ( time == 4 ) {
        time = 18.5*60*60*1000;
    } else if ( time == 5 ) {
        time = 24*60*60*1000;
    }
    return time;
}

function pad(n){return n<10 ? '0'+n : n;}

function toggleInOut(obj) {
    $('#graphs').prepend($('#noDataAlert').clone().attr('id','noData'));
}

function setTimeFields() {
    $('#startDate').val(dateFormatISO(startTime));
    $('#endDate').val(dateFormatISO(endTime));
    $('#startTime').val(timeFormat(startTime));
    $('#endTime').val(timeFormat(endTime));
}

function loading() {
    noty({
        "text": "<strong>Loading data, please wait...</strong>",
        "theme":"noty_theme_twitter",
        "layout":"center",
        "type":"information",
        "animateOpen":{"opacity":"toggle"},
        "animateClose":{"opacity":"toggle"},
        "speed":500,
        "timeout":10000,
        "closeButton":false,
        "closeOnSelfClick":true,
        "closeOnSelfOver":false,
        "modal":false});
}

function errorNoData(sName) {
    noty({
        "text": "<strong>Error: No data availbale for sensor '" + sName + "' in selected time period!</strong>",
        "theme":"noty_theme_twitter",
        "layout":"top center",
        "type":"error",
        "animateOpen":{"height":"toggle"},
        "animateClose":{"height":"toggle"},
        "speed":500,
        "timeout":1000,
        "closeButton":false,
        "closeOnSelfClick":true,
        "closeOnSelfOver":false,
        "modal":false});
}

function generalError(jqXHR, textStatus, errorThrown) {
    noty({
        "text": "<strong>Error: Something wrong happened: "+textStatus+" - "+errorThrown+"!</strong>",
        "theme":"noty_theme_twitter",
        "layout":"top center",
        "type":"error",
        "animateOpen":{"height":"toggle"},
        "animateClose":{"height":"toggle"},
        "speed":500,
        "timeout":2000,
        "closeButton":false,
        "closeOnSelfClick":true,
        "closeOnSelfOver":false,
        "modal":false});
}

function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5,
        border: '1px solid #fdd',
        padding: '2px',
        'background-color': '#fee',
        opacity: 0.80
    }).appendTo("body").show();
}

// sensor tooltips
function initTooltips() {

    $('.sensor-name').each(function(i, elem) {
        var content = $(this).find('.tooltip-content').html();
        var title = $(this).find('a').first().text();

        $(this).popover({
            content: content,
            title: title
        });
    });
}
