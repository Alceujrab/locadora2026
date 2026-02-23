<script src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>
<link href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />

<style type="text/css">
  #visualization {
    width: 100%;
    border: 1px solid lightgray;
    background: #ffffff;
    border-radius: 8px;
    padding: 10px;
    margin-top: 20px;
  }
  
  /* Custom styles for the timeline items based on status */
  .vis-item.vis-range {
      border-radius: 4px;
      font-size: 13px;
      padding: 4px 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.12);
  }
  
  .vis-item.vis-background {
      background-color: rgba(220, 220, 220, 0.4);
  }

  .vis-item-content {
      font-weight: 500;
  }
</style>

<div x-data="timelineInit()" x-init="initTimeline">
    
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-bold">Calendário de Reservas e Manutenções</h2>
            <p class="text-sm text-gray-500 mt-1">Visão geral logística da frota (Próximos 30 dias)</p>
        </div>
        
        <div class="flex gap-2">
            <template x-if="isLoading">
                <span class="text-sm text-gray-500 animate-pulse">Carregando dados...</span>
            </template>
            <button @click="resetZoom" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm font-semibold transition">
                Mês Atual
            </button>
        </div>
    </div>

    <div class="flex gap-4 mb-4 text-xs font-semibold">
        <span class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-blue-500"></div> Nova Reserva</span>
        <span class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-yellow-500"></div> Confirmada/Caução</span>
        <span class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-green-500"></div> Em Andamento</span>
        <span class="flex items-center gap-1"><div class="w-3 h-3 rounded-full bg-red-500"></div> Manutenção / Alerta</span>
    </div>

    <div id="visualization" style="height: 600px;"></div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timelineInit', () => ({
            timeline: null,
            isLoading: true,
            
            initTimeline() {
                var container = document.getElementById('visualization');
                
                // Set default options
                var options = {
                    groupOrder: 'content', 
                    editable: false,
                    margin: { item: 10, axis: 5 },
                    orientation: 'top',
                    stack: false,
                    zoomMin: 1000 * 60 * 60 * 24, // One day in milliseconds
                    zoomMax: 1000 * 60 * 60 * 24 * 31 * 6, // about 6 months
                    start: new Date(new Date().setDate(new Date().getDate() - 3)), // 3 days ago
                    end: new Date(new Date().setDate(new Date().getDate() + 27)), // 27 days from now
                    locale: 'pt-br',
                    timeAxis: {scale: 'day', step: 1}
                };
                
                // Initialize timeline with empty data first
                this.timeline = new vis.Timeline(container, new vis.DataSet([]), new vis.DataSet([]), options);
                
                // Fetch real data
                this.loadData();
            },
            
            loadData() {
                this.isLoading = true;
                
                // Make API call to MoonShine endpoint
                fetch('/admin/api/calendar-timeline-events')
                    .then(response => response.json())
                    .then(data => {
                        this.timeline.setGroups(new vis.DataSet(data.groups));
                        this.timeline.setItems(new vis.DataSet(data.items));
                        this.isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error loading timeline data:', error);
                        this.isLoading = false;
                        alert('Erro ao carregar o calendário de frotas.');
                    });
            },
            
            resetZoom() {
                if(this.timeline) {
                    this.timeline.setWindow(
                        new Date(new Date().setDate(new Date().getDate() - 3)),
                        new Date(new Date().setDate(new Date().getDate() + 27))
                    );
                }
            }
        }));
    });
</script>
