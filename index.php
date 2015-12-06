<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kanban Board</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
    body {
        padding-top: 50px;
    }
    #todo, #doing, #done {
        min-height: 20px;
    }
    </style>
</head>
<body>
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a href="#" class="navbar-brand">AppzCoder</a>
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1 id="type">Kanban Board <a href="#" class="btn btn-primary pull-right btn-sm" data-toggle="modal" data-target="#newTaskModal">Add Task</a></h1>
        </div>
        <div class="row" id="board">
            <div id="todo-container">
            </div>

            <div id="doing-container">
            </div>

            <div id="done-container">
            </div>
        </div>
        <button class="btn btn-danger" id="reset-data">Reset</button>

        <!-- Modal -->
        <div class="modal fade" id="newTaskModal" tabindex="-1" role="dialog" aria-labelledby="newTaskModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Your Task</h4>
                    </div>
                    <div class="modal-body">
                        <form id="new-task">
                            <div class="form-group">
                                <input type="text" class="form-control" id="task_name" name="task_name">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="close-add-new-task">Close</button>
                        <button type="button" class="btn btn-primary" id="add-new-task">Add</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="container"></div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery-ui.min.js"></script>
    <script src="assets/js/jquery.ui.touch-punch.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react-dom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore.js"></script>

    <script type="text/babel">

        var TaskList = React.createClass({
            render: function() {
                var type = this.props.type;
                var className = this.props.className;
                var displayTask = this.props.items.map(function (task) {
                    if (type == task.type) {
                        return <li className={className} data-id={task.id}>{task.task}</li>
                    }
                });

                // Make tasks list sortable
                $("#todo, #doing, #done").sortable({
                    revert: true,
                    cursor: "move",
                    connectWith: ".connected"
                });

                return (
                    <ul className="list-group connected" id={this.props.type}>
                        {displayTask}
                    </ul>
                );
            },
            dragEnd: function(e) {
                console.log(e);
            },
        });

        var TaskApp = React.createClass({
            getInitialState: function() {
                return {
                    items: [],
                    task: ''
                };
            },
            componentDidMount: function() {
                $.ajax({
                    url: this.props.url,
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        this.setState({items: data});
                    }.bind(this),
                    error: function(xhr, status, err) {
                        console.error(this.props.url, status, err.toString());
                    }.bind(this)
                });
            },
            onChange: function(e) {
                var task = e.target.value;
                this.setState({ task });
            },
            addTask: function(e) {
                $.post( "ajax/ajax.php", { "task": this.state.task, "type": this.props.type })
                    .done(function( id ) {
                        this.setState({
                            items: this.state.items.concat(
                                [{ "id": id, "task": this.state.task, "type": this.props.type }]
                            ),
                            task: ''
                        });
                }.bind(this));

                e.preventDefault();
            },
            addTaskCustom: function(task) {
                $.post( "ajax/ajax.php", { "task": this.state.task, "type": this.props.type })
                    .done(function( id ) {
                        this.setState({
                            items: this.state.items.concat(
                                [{ "id": id, "task": task, "type": "todo" }]
                            ),
                            task: ''
                        });
                }.bind(this));
            },
            render: function(){
                var title;
                var containerClassName;
                var ulClassName;

                if (this.props.type == 'todo') {
                    title = 'To Do';
                    containerClassName = 'panel panel-warning';
                    ulClassName = 'list-group-item list-group-item-warning';
                } else if (this.props.type == 'doing') {
                    title = 'Doing';
                    containerClassName = 'panel panel-info';
                    ulClassName = 'list-group-item list-group-item-info';
                } else {
                    title = 'Done';
                    containerClassName = 'panel panel-success';
                    ulClassName = 'list-group-item list-group-item-success';
                }

                return (
                    <div className="col-sm-4">
                        <div className={containerClassName}>
                            <div className="panel-heading">
                                <h3 className="panel-title">{title}</h3>
                            </div>
                            <div className="panel-body">
                                <TaskList items={this.state.items} className={ulClassName} type={this.props.type} />
                                <form onSubmit={this.addTask}>
                                    <input onChange={this.onChange} value={this.state.task} />
                                    <button>Add Task</button>
                                </form>
                            </div>
                        </div>
                    </div>
                );
            }
        });

        var $todoApp = ReactDOM.render(<TaskApp type="todo" url="ajax/tasks.json" />, document.getElementById('todo-container'));
        var $doingApp = ReactDOM.render(<TaskApp type="doing" url="ajax/tasks.json" />, document.getElementById('doing-container'));
        var $doneApp = ReactDOM.render(<TaskApp type="done" url="ajax/tasks.json" />, document.getElementById('done-container'));

        // Ading task item
        $( "button#add-new-task" ).click(function( e ) {
            e.preventDefault();
            var taskName = $("input#task_name").val();
            $( "button#close-add-new-task" ).trigger( "click" );
            $("input#task_name").val('');
            $todoApp.addTaskCustom(taskName);
        });

        // Ading task item
        $( "button#reset-data" ).click(function( e ) {
            e.preventDefault();

            $.post( "ajax/ajax.php", { "reset": true }).done(function(){
                location.reload();
            });
        });

    </script>
</body>
</html>
