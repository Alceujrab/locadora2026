<div id="calendar"></div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'pt-br',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: '/admin/api/calendar-events',
      eventClick: function(info) {
        info.jsEvent.preventDefault(); // don't let the browser navigate
        if (info.event.url) {
          window.location.href = info.event.url;
        }
      }
    });
    calendar.render();
  });
</script>

<style>
    /* Algumas correções para o FullCalendar rodar bem dentro do card do MoonShine */
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        font-family: inherit;
        background: var(--background);
        border-radius: 8px;
        padding: 10px;
    }
    .fc-event {
        cursor: pointer;
    }
</style>
