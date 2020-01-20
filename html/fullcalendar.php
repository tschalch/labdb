<?php
require_once('accesscontrol.php');
require_once('bdd.php');
require_once("header.php");

$resource = isset($_GET['resource']) ? $_GET['resource']: NULL;
$sql = "SELECT id, title, description, start, end, color FROM events WHERE resource='$resource';";

$req = $bdd->prepare($sql);
$req->execute();

$events = $req->fetchAll();

$sql = "SELECT * FROM resources WHERE resourceID='$resource';";
$req = $bdd->prepare($sql);
$req->execute();
$resource = $req->fetch();

?>
<link href='fc4/packages/core/main.css' rel='stylesheet' />
<link href='fc4/packages/daygrid/main.css' rel='stylesheet' />
<link href='fc4/packages/timegrid/main.css' rel='stylesheet' />
<script src='fc4/packages/core/main.js'></script>
<script src='fc4/packages/interaction/main.js'></script>
<script src='fc4/packages/daygrid/main.js'></script>
<script src='fc4/packages/timegrid/main.js'></script>
<script src="https://unpkg.com/popper.js"></script>
<script src="https://unpkg.com/tooltip.js"></script>
<script>
jQuery( document ).ready(function( $ ) {
  function updateEvent(info){
    ev = { 
      id: info.event.id.toString(),
      start: info.event.start.toISOString(),
      end: info.event.end.toISOString() 
    }
    var str_json = JSON.stringify(ev)
    request= new XMLHttpRequest()
    request.open("POST", "event_handler.php", true)
    request.setRequestHeader("Content-type", "application/json")
    request.send(str_json)
  }
  function addEvent(calendar, info, callback){
    ev = { 
      start: info.start.toISOString(),
      end: info.end.toISOString(),
      title: <?php print "'$userfullname'"; ?>,
      description: '',
      color: <?php print "'$usercolor'"; ?>,
      user: <?php print "'$userid'"; ?>,
      resource: <?php print "'${resource['resourceID']}'"; ?>
    }
    var str_json = JSON.stringify(ev)
    request= new XMLHttpRequest()
    request.open("POST", "addEvent.php", true)
    request.setRequestHeader("Content-type", "application/json")
    request.send(str_json)
    request.onreadystatechange= function () {
        if (request.readyState == 4 && request.status == 200) {
            ev.id = request.responseText
            callback.apply(this, [calendar, ev]);
        }else{

        }
    }; 
  }
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: ['interaction', 'dayGrid', 'timeGrid'],
    editable: true,
    selectable: true,
    eventDrop: function(info) {
      //alert(info.event.title + " was dropped on " + info.event.start.toISOString());
      if (confirm("Are you sure about this change?")) {
        updateEvent(info);
      } else {
        info.revert();
      }
    },
    eventResize: function(info) {
      //alert(info.event.title + " was dropped on " + info.event.start.toISOString());
      if (confirm("Are you sure about this change?")) {
        updateEvent(info);
      } else {
        info.revert();
      }
    },
    eventRender: function(info) {
       var el = info.el.querySelector('[class=fc-title]')
       el.innerHTML += '<div class="hr-line-solid-no-margin"></div><span style="font-size: 0.85em">'+ info.event.extendedProps.description +'</span></div>'
      /*var tooltip = new Tooltip(info.el, {
        title: info.event.extendedProps.description,
        placement: 'top',
        trigger: 'hover',
        container: 'body'
      });*/
    },
    select: function(info) {
      //alert(info.event.title + " was dropped on " + info.event.start.toISOString());
      if (confirm("Want to add a new event?")) {
        addEvent(this, info, function(cal, ev) {
          cal.addEventSource([ev])
          cal.render()
      });
      } else {
      }
    },
    eventClick: function(info) {
      info.jsEvent.preventDefault(); // don't let the browser navigate
      $('#eventModal #id').val(info.event.id.toString())
      $('#eventModal #title').val(info.event.title.toString())
      $('#eventModal #color').val(info.event.backgroundColor.toString())
      $('#eventModal #description').val(info.event.extendedProps.description.toString())
      $('#eventModal').modal('show');
    },
    header: {
              left: 'prev, next today',
              center: 'title',
              right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
    defaultView: 'timeGridWeek',
    eventSources: [
      {
        url: 'events.php?resource=<?php print $resource['resourceID'] ?>',
        color: 'yellow',   // an option!
        textColor: 'black' // an option!
      }
  ]
  });
  calendar.render();
});
</script>
<style>
#calendar .fc-day-header a {
  color: black !important;
}
	  #calendar a.fc-event {
  color: #fff !important; /* bootstrap default styles make it black. undo */
  background-color: #0065A6;
}
.popper,
.tooltip {
  position: absolute;
  opacity: 1;
  z-index: 9999;
  background: white;
  color: black;
  width: 150px;
  border-radius: 3px;
  box-shadow: 0 0 2px rgba(0,0,0,0.5);
  padding: 10px;
  text-align: center;
}
.tooltip-inner{
  background: white;
  color: black;
}
.style5 .tooltip {
  background: #1E252B;
  color: white;
  max-width: 200px;
  width: auto;
  font-size: .8rem;
  padding: .5em 1em;
}
.popper .popper__arrow,
.tooltip .tooltip-arrow {
  width: 0;
  height: 0;
  border-style: solid;
  position: absolute;
  margin: 5px;
}

.tooltip .tooltip-arrow,
.popper .popper__arrow {
  border-color: white;
}
.style5 .tooltip .tooltip-arrow {
  border-color: #1E252B;
}
.popper[x-placement^="top"],
.tooltip[x-placement^="top"] {
  margin-bottom: 5px;
}
.popper[x-placement^="top"] .popper__arrow,
.tooltip[x-placement^="top"] .tooltip-arrow {
  border-width: 5px 5px 0 5px;
  border-left-color: transparent;
  border-right-color: transparent;
  border-bottom-color: transparent;
  bottom: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}
.popper[x-placement^="bottom"],
.tooltip[x-placement^="bottom"] {
  margin-top: 5px;
}
.tooltip[x-placement^="bottom"] .tooltip-arrow,
.popper[x-placement^="bottom"] .popper__arrow {
  border-width: 0 5px 5px 5px;
  border-left-color: transparent;
  border-right-color: transparent;
  border-top-color: transparent;
  top: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}
.tooltip[x-placement^="right"],
.popper[x-placement^="right"] {
  margin-left: 5px;
}
.popper[x-placement^="right"] .popper__arrow,
.tooltip[x-placement^="right"] .tooltip-arrow {
  border-width: 5px 5px 5px 0;
  border-left-color: transparent;
  border-top-color: transparent;
  border-bottom-color: transparent;
  left: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}
.popper[x-placement^="left"],
.tooltip[x-placement^="left"] {
  margin-right: 5px;
}
.popper[x-placement^="left"] .popper__arrow,
.tooltip[x-placement^="left"] .tooltip-arrow {
  border-width: 5px 0 5px 5px;
  border-top-color: transparent;
  border-right-color: transparent;
  border-bottom-color: transparent;
  right: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}
.fc-event{
font-size: 1em !important;
}
div.fc-title{
font-weight: bold;
}

</style>
</head>
<body>

<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form class="form-horizontal" method="POST" action="event_handler.php">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">Edit Event</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
        <label for="title" class="col-sm-2 control-label">Title</label>
        <div class="col-sm-10">
          <input type="text" name="title" class="form-control" id="title" placeholder="Title">
        </div>
        </div>
        <div class="form-group">
        <label for="description" class="col-sm-2 control-label">Description</label>
        <div class="col-sm-10">
          <input type="text" name="description" class="form-control" id="description" placeholder="Description">
        </div>
        </div>
       <div class="form-group">
          <label for="color" class="col-sm-2 control-label">Color</label>
        <div class="col-sm-10">
          <input type="text" name="color" class="form-control" id="color" list="select-list-id"/>
       </div>
       </div>
       <datalist id="select-list-id">
          <option style="color:#0071c5;" value="#0071c5">&#9724; Dark blue</option>
          <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquoise</option>
          <option style="color:#008000;" value="#008000">&#9724; Green</option>
          <option style="color:#FFD700;" value="#FFD700">&#9724; Yellow</option>
          <option style="color:#FF8C00;" value="#FF8C00">&#9724; Orange</option>
          <option style="color:#FF0000;" value="#FF0000">&#9724; Red</option>
          <option style="color:#000;" value="#000">&#9724; Black</option>
       </datalist>
          <div class="form-group"> 
          <div class="col-sm-2">
            <label onclick="toggleCheck('check1');" class="label-off" for="check1" id="check1_label">
            Delete
          </label>
          <input class="nocheckbox" type="checkbox" id="check1" name="delete">
          </div>
        </div>
        <script>
        function toggleCheck(check) {
          if ($('#'+check).is(':checked')) {
            $('#'+check+'_label').removeClass('label-on');
            $('#'+check+'_label').addClass('label-off');
          } else {
            $('#'+check+'_label').addClass('label-on');
            $('#'+check+'_label').removeClass('label-off');
          }
        }		  
        </script>
        <input type="hidden" name="id" class="form-control" id="id">
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
    </div>
  </div>
</div>
<?php 
require_once("navigation.php");
print "<div class=\"container\">";
print "<div id=\"title\"><h2 >Reservations for ${resource['resource_name']}</h2></div>";

?>
<div id='calendar'></div>

<?php
include("footer.php");

?>