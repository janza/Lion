$(function() {

    $("#refresh_table_button").live('click',function() {
	$('#control_form').load("http://161.53.67.224/lion/index.php/systemManaging/managingRefreshTable").fadeIn("slow");
    });

});