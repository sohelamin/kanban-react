		$(function() {
			populateTask();

			$("#todo, #doing, #done").sortable({
				revert: true,
				cursor: "move",
				connectWith: ".connected",
				stop: saveToJSON
			});	
			$("#todo, #doing, #done").bind("sortreceive",function(event,ui){
				var element = $(ui.item[0]);
				var elementParentId = $(ui.item[0]).parent().attr('id');
				if ( elementParentId == 'todo' ) {
					element.removeClass("list-group-item-info");
					element.removeClass("list-group-item-success");
					element.addClass("list-group-item-warning");
				} else if ( elementParentId == 'doing' ) {
					element.removeClass("list-group-item-warning");
					element.removeClass("list-group-item-success");
					element.addClass("list-group-item-info");
				} else {
					element.removeClass("list-group-item-info");
					element.removeClass("list-group-item-warning");
					element.addClass("list-group-item-success");
				}
			});

			$( "#reset-data" ).click(function( event ) {
				event.preventDefault();

				$.post("ajax/ajax.php", { reset: true }, function (data) {
					$("#todo, #doing, #done").html('');
					populateTask();
				});
			});

			$( "button#add-new-task" ).click(function( event ) {
				event.preventDefault();

				var ids = [];
				$('#board li').each(function(){
				    ids.push($(this).attr('data-id'));
				});

				var id = Math.max.apply(Math, ids);
				if( !isFinite(id) ) { id = 0; }
				id++;
				var taskName = $("input#task_name").val();
				$( "button#close-add-new-task" ).trigger( "click" );
				$("input#task_name").val('');
				var task = '<li class="list-group-item list-group-item-warning" data-id="' + id + '">' + taskName + '<a href="#" onclick="del(this);"><i class="glyphicon glyphicon-remove pull-right"></i></a></li>';
				$( task ).appendTo( "#todo" );

				saveToJSON();
			});			
			

		});

		function populateTask() {
	 		$.getJSON( "ajax/tasks.json", function( data ) {

				var todoTasksList = [];
				var doingTasksList = [];
				var doneTasksList = [];
				$.each( data, function( key, val ) {
					if (val.type=='todo') {
						todoTasksList.push( '<li class="list-group-item list-group-item-warning" data-id="' + val.id + '">' + val.task + '<a href="#" onclick="del(this);"><i class="glyphicon glyphicon-remove pull-right"></i></a></li>' );
					} else if (val.type=='doing') {
						doingTasksList.push( '<li class="list-group-item list-group-item-info" data-id="' + val.id + '">' + val.task + '<a href="#" onclick="del(this);"><i class="glyphicon glyphicon-remove pull-right"></i></a></li>' );
					} else {
						doneTasksList.push( '<li class="list-group-item list-group-item-success" data-id="' + val.id + '">' + val.task + '<a href="#" onclick="del(this);"><i class="glyphicon glyphicon-remove pull-right"></i></a></li>' );
					}
				});
				$( todoTasksList.join( "" ) ).appendTo( "#todo" );
				$( doingTasksList.join( "" ) ).appendTo( "#doing" );
				$( doneTasksList.join( "" ) ).appendTo( "#done" );

			});
		}

		function saveToJSON() {
			var tasksItems = [];
			// loop trough the li
			$("#board li").each(function () {
				//push element data to the array
				var id = $(this).attr('data-id');
				var type = 'todo';
				var itemParent = $(this).parent('ul').attr('id');
				if (itemParent=='todo') {
					type = 'todo';
				} else if (itemParent=='doing') {
					type = 'doing';
				} else {
					type = 'done';
				}
				tasksItems.push({
					id : id,
					task : $(this).text(),
					type: type
				});
			});

			//console.log(JSON.stringify(allItems));

			// then you can simply pass it to the post method 
			$.post("ajax/ajax.php", { data: JSON.stringify(tasksItems) }, function (data) {
			// recived data
			});

		}

		function del(ele) {
			$(ele).parent('li').remove();
			saveToJSON();
			return false;
		}