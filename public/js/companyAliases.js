function setAliasListeners(){
	$('#alias-add').click(function(){
		showAddAlias();
	});
	
	$('.remove-alias').each(function(){
		$(this).click(function(){
			removeAlias($(this).attr('id').replace("alias-", ""));
		});
	});
	
	$('#clear-aliases').click(function(){
		clearaliases();
	});
}

function showAddAlias(){
	if($('#add-alias').length != 0) return;

	var html = "<div id='new-alias-div' class='row col-md-12 bg-primary'>";
	html += "<div class='col-md-2'><input type='text' id='alias-input' class='form-control' /></div>";
	html += "<div class='col-md-2'><input type='button' value='Add' id='alias-add-button' class='btn btn-primary form-control' /></div>";
	html += "</div>";

	$('#aliases').append(html);
	
	$('#alias-add-button').click(function(){
		addAlias($('#alias-input').val());
	});
}
function addAlias(name){
	$.post('/users/ajaxAddCompanyAlias', {
		'alias': name
	}).done(function(id){
		$('#new-alias-div').remove();
		
		var html = "<div class='row col-md-12'>";
		html += "<div class='col-md-2'>" + name + "</div>";
		html += "<div class='col-md-2'><input type='button' value='Remove' id='alias-" + id + "' class='btn btn-primary form-control remove-alias' /></div>";
		html += "</div>";
		
		$('#aliases').append(html);
		
		$('#alias-' + id).click(function(){
			removeAlias($(this).attr('id'));
		});
	});
}

function removeAlias(id){
	$.post('/users/ajaxRemoveCompanyAlias', {
		'id': id
	}).done(function(response){
		$('#aliasrow-' + id).remove();
	});
}

function clearaliases(){
	$('.alias-definition').each(function(){
		removeAlias($(this).attr('id'));
	});
}

$(document).ready(function(){
	setAliasListeners();
});
