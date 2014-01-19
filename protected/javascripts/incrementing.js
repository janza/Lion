$(function() {

    //$('.control_subsystem').append('<div class="inc button">+</div><div class="dec button">-</div>');

    $(".button").live('click',function() {
	var $button = $(this);
	var oldValue = $button.parent().find("input").val();
	var newVal = 0;
	if ($button.text() == "+") {
	    if (parseFloat(oldValue)>=5)
		newVal = 5;
	    else if (parseFloat(oldValue) < 0)
		newVal = 0;
	    else
		newVal = parseFloat(oldValue) + 1;
	// AJAX save would go here
	} else {
	    // Don't allow decrementing below zero
	    if (parseFloat(oldValue) >= 1) {
		if (parseFloat(oldValue)>5)
		    newVal = 5;
		else
		    newVal = parseFloat(oldValue) - 1;
	    // AJAX save would go here
	    }
	    else
		newVal = 0;
	}
	$button.parent().find("input").val(newVal);
    });

    $(".button_temp").live('click',function() {
	var $button = $(this);
	var oldValue = $button.parent().find("input").val();
	var newVal = 0;
	if ($button.text() == "+") {
	    newVal = parseFloat(oldValue) + 1;
	// AJAX save would go here
	} else {
	    newVal = parseFloat(oldValue) - 1;
	}
	$button.parent().find("input").val(newVal);
    });

});