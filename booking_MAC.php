<?php
include_once "header.php";

$active[2] = "";
$active[1] = "";
$active[0] = "class=active";
?>
<body>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#"><?php echo "Welcome ". get_name($_SESSION['email']); ?></a>
		</div>
		<ul class="nav navbar-nav">
			<li <?php echo $active[0] ?>><a href="booking_MAC.php">MAC Suite</a></li>
			<li <?php echo $active[1] ?>><a href="booking_Gilpin.php">Gilpin IT Suite</a></li>
			<li <?php echo $active[2] ?>><a href="booking_Hartford.php">Hartford IT Suite</a></li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
		</ul>
		</div>
	</nav>
  <div class="container-fluid">
    <div class="main">
      <div style="display:flex">
        <div style="">
        <div id="nav"></div>
        </div>
        <div style="flex-grow: 1; margin-left: 10px;">
        <div class="toolbar buttons">
          <span class="toolbar-item"><a id="buttonDay" href="#">Day</a></span>
          <span class="toolbar-item"><a id="buttonWeek" href="#">Week</a></span>
          <span class="toolbar-item"><a id="buttonMonth" href="#">Month</a></span>
        </div>
        <div id="dpDay"></div>
        <div id="dpWeek"></div>
        <div id="dpMonth"></div>
        </div>
      </div>
    </div>
    
  <script type="text/javascript">


    var nav = new DayPilot.Navigator("nav");
    nav.showMonths = 3;
    nav.skipMonths = 3;
    nav.init();

    var day = new DayPilot.Calendar("dpDay");
    day.viewType = "Day";
    day.dayBeginsHour = 8;
    day.dayEndsHour = 17;
    configureCalendar(day);
    day.init();

    var week = new DayPilot.Calendar("dpWeek");
    week.headerDateFormat = "d/MM/yyyy",
    week.viewType = "Week";
    week.dayBeginsHour = 8;
    week.dayEndsHour = 17;
    configureCalendar(week);
    week.init();

    var month = new DayPilot.Month("dpMonth");
    configureCalendar(month);
    month.init();


    function configureCalendar(dp) {
      
      dp.contextMenu = new DayPilot.Menu({
        items: [
          {
            text: "Blue",
            icon: "icon icon-blue",
            color: "#3d85c6",
            onClick: function(args) { updateColor(args.source, args.item.color); }
          },
          {
            text: "Green",
            icon: "icon icon-green",
            color: "#6aa84f",
            onClick: function(args) { updateColor(args.source, args.item.color); }
          },
          {
            text: "Orange",
            icon: "icon icon-orange",
            color: "#e69138",
            onClick: function(args) { updateColor(args.source, args.item.color); }
          },
          {
            text: "Red",
            icon: "icon icon-red",
            color: "#cc4125",
            onClick: function(args) { updateColor(args.source, args.item.color); }
          }
        ]
      });


      dp.onBeforeEventRender = function(args) {
        if (!args.data.backColor) {
          args.data.backColor = "#6aa84f";
        }
        args.data.borderColor = "darker";
        args.data.fontColor = "#fff";
        args.data.barHidden = true;

        args.data.areas = [
          {
            right: 2,
            top: 2,
            width: 20,
            height: 20,
            html: "&equiv;",
            action: "ContextMenu",
            cssClass: "area-menu-icon",
            visibility: "Hover"
          }
        ];
      };
    dp.eventMoveHandling = "Disabled";
    dp.eventResizeHandling = "Disabled";
      // event creating
      dp.onTimeRangeSelected = function (args) {
      var diff = args.end.getMinutes() - args.start.getMinutes() + (args.end.getHours() - args.start.getHours()) * 60;
      if(diff > 180) {
        alert("Selected timeline is bigger than 3 hours");
        dp.clearSelection();
      }
      else {
        var form = [
          {name: "Room", id: "room", disabled: true},
          {name: "Name", id: "text", disabled: true},
          {name: "Start", id: "start", dateFormat: "MMMM d, yyyy h:mm tt", disabled: true},
          {name: "End", id: "end", dateFormat: "MMMM d, yyyy h:mm tt", disabled: true},
        ];

        var data = {
          start: args.start,
          end: args.end,
          text: "<?php echo get_name($_SESSION['email']); ?>",
          room: "MAC Suite"
        };

        DayPilot.Modal.form(form, data).then(function(modal) {
          dp.clearSelection();

          if (modal.canceled) {
            return;
          }

          DayPilot.Http.ajax({
          url: "calendar/calendar_create.php",
            data: modal.result,
            success: function(ajax) {
              var dp = switcher.active.control;
              dp.events.add({
                start: data.start,
                end: data.end,
                id: ajax.data.id,
                text: data.text,
                room: data.room
              });
            }
          });
        });
      }
        
      };

      dp.onEventClick = function(args) {
        DayPilot.Modal.alert(args.e.data.room + " is booked by " + args.e.data.text + " between " + args.e.data.start + " ~ " + args.e.data.end);
      };
    }

    var switcher = new DayPilot.Switcher({
      triggers: [
        {id: "buttonDay", view: day },
        {id: "buttonWeek", view: week},
        {id: "buttonMonth", view: month}
      ],
      navigator: nav,
      selectedClass: "selected-button",
      onChanged: function(args) {
        args.room = "MAC Suite";
        console.log(args);
        switcher.events.load("calendar/calendar_events_mac.php");
      }
    });

    switcher.select("buttonWeek");

    function updateColor(e, color) {
      var params = {
        id: e.data.id,
        color: color
      };
      DayPilot.Http.ajax({
        url: "calendar/calendar_color.php",
        data: params,
        success: function(ajax) {
          var dp = switcher.active.control;
          e.data.backColor = color;
          dp.events.update(e);
          dp.message("Color updated");
        }
      });
    }
  </script>


<?php
include_once "footer.php";
?>