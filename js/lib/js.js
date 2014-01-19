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
        }
    },
    crosshair: {
        mode: "x"
    },
    xaxis: {
        mode: "time"
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
    var uniqueID = getUniqueID(sensorID, unitID);
    var toggled = unitLabel.toggleClass('label-success').hasClass('label-success');


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
            'unit_name': unitName
        });
    }

    if (activeUnits.length) {
        getSensorData();
    }

    return false;

}



function getSensorData() {

    var dayDiff = getDayDiff( startTime, endTime );
    var dayPartDiff = getDayPartDiff( startTime, endTime );
    var hourDiff = getHourDiff( startTime, endTime );
    var method = 'LReadings';
    var units = activeUnits;

    var dataArray = {};


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

    console.log(method);

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
    date = date.slice(0,4) + '-' + date.slice(4,6) + '-' + date.slice(6,8);
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
    date = date.slice(0,4) + '-' + date.slice(4,6) + '-' + date.slice(6,8);

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
    date = date.slice(0,4) + '-' + date.slice(4,6) + '-' + date.slice(6,8);

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


    if ( table === 'LReadings' ) {
        formatDate = formatLReadings;
    } else if ( table === 'AggregateDayPart' ) {
        formatDate = formatDayPart;
    } else if ( table === 'AggregateDay' ) {
        formatDate = formatDay;
    }

    // initiate sensor info
    for ( var i in activeUnits ) {
        var elem = activeUnits[i];
        var uniqueID = getUniqueID( elem.sensor_id, elem.unit_id );

        tempData[uniqueID] = {
            label: sensors[elem.sensor_id] + elem.unit_id,
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
            value = (elem.value || elem.avg_value),
            data = [elem.date_id, (elem.time_id || elem.day_part_id)],
            prevData = data, missing;

        if (tempData[uniqueID].data.length) {
            prevData = tempData[uniqueID].data.slice(-1)[0][2];
        } else {
            tempData[uniqueID].data.push([startTime*1- endTime.getTimezoneOffset()*60*1000, null, null]);
        }

        timeinfo = formatDate( data[0], data[1], prevData );
        time = timeinfo[0];
        missing = timeinfo[1];

        if (missing) {
            tempData[uniqueID].data.push([ time+1, null ]);
        }

        newPoint = [time, value, [elem.date_id, (elem.time_id || elem.day_part_id)]];

        tempData[uniqueID].data.push(newPoint);

    });

    // return data
    for( i in tempData ) {
        tempData[i].data.push([endTime*1- endTime.getTimezoneOffset()*60*1000, null, null]);
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
            var someData = true;
            var j = 0;
            
            $graphs.append(graphs);
            plot = $.plot( $('#graph'+i), graphData[i], plotOptions );
        

        }

        bindSelection();
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
        console.log(gsns, gsnArray);
        initTooltips();
    });

}


function init() {

    getSensors();

    /* Init time values */
    setTimeFields();

    initHooks();
}


$(init);
