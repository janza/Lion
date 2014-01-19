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

function dateFormat(date) {
	return ''+date.getFullYear()+pad(date.getMonth()+1)+pad(date.getDate());
}

function dateFormatNice(date) {
	return ''+pad(date.getMonth()+1)+'/'+pad(date.getDate())+'/'+date.getFullYear();
}

function timeFormat(date) {
	return ''+pad(date.getHours())+':'+pad(date.getMinutes());
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

function errorNoData () {
    noty({
        "text": "<strong>No data availbale for selected period!</strong>",
        "theme":"noty_theme_twitter",
        "layout":"top",
        "type":"error",
        "animateOpen":{"height":"toggle"},
        "animateClose":{"height":"toggle"},
        "speed":500,
        "timeout":5000,
        "closeButton":false,
        "closeOnSelfClick":true,
        "closeOnSelfOver":false,
        "modal":false});
}



// sensor tooltips
function initTooltips() {

    $('.sensor-name').each(function(i, elem) {
        var content = $(this).find('.tooltip-content').html();
        var title = $(this).find('a').text();

        $(this).popover({
            content: content,
            title: title
        });
    });
}