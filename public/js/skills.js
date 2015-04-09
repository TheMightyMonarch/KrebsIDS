function setSkillListeners(){
	$('#skill-add').click(function(){
		showAddSkill();
	});
	$('.remove-skill').each(function(){
		$(this).click(function(){
			removeSkill($(this).attr('id').replace("skill-", ""));
		});
	});
	
	$('#clear-skills').click(function(){
		clearSkills();
	});
}

function showAddSkill(){
	if($('#add-skill').length != 0) return;

	var html = "<div id='new-skill-div' class='row col-md-12 bg-primary'>";
	html += "<div class='col-md-2'><input type='text' id='skill-input' class='form-control' /></div>";
	html += "<div class='col-md-2'><input type='button' value='Add' id='skill-add-button' class='btn btn-primary form-control' /></div>";
	html += "</div>";

	$('#skills').append(html);
	
	$('#skill-add-button').click(function(){
		addSkill($('#skill-input').val());
	});
}
function addSkill(name){
	$.post('/users/ajaxAddSkill', {
		'skill': name
	}).done(function(id){
		$('#new-skill-div').remove();
		
		var html = "<div class='row col-md-12'>";
		html += "<div class='col-md-2'>" + name + "</div>";
		html += "<div class='col-md-2'><input type='button' value='Remove' id='skill-" + id + "' class='btn btn-primary form-control remove-skill' /></div>";
		html += "</div>";
		
		$('#skills').append(html);
		
		$('#skill-' + id).click(function(){
			removeSkill($(this).attr('id'));
		});
	});
}

function removeSkill(id){
	$.post('/users/ajaxRemoveSkill', {
		'id': id
	}).done(function(response){
		$('#skillrow-' + id).remove();
	});
}

function clearSkills(){
	$('.skill-definition').each(function(){
		removeSkill($(this).attr('id'));
	});
}

$(document).ready(function(){
	setSkillListeners();
});
