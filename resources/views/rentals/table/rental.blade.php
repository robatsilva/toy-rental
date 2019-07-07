<style>
    .th-time{
        background-color: #EEE;
    }
    tbody tr:hover{
        background-color: #DDD;
    }

    .status{
        width: 180px;
    }

    td{
        vertical-align: middle !important;
    }
</style>
<table class="table">
    <thead>
        <tr>
            <th class="text-center">Brinquedo</th>
            <th class="text-center">Cliente</th>
            <th class="text-center">Início</th>
            <th class="text-center">Retorno</th>
            <th class="text-center">Valor a pagar (R$)</th>
            <th class="text-center">Status</th>
            <th class="text-center">Ação</th>
        </tr>
    </thead>
    <tbody id="rental-body">
        @foreach($rentals as $rental)
        
        <tr class="text-center" @if($rental->time_exceded > 0) style="background-color: #ffa1a1" @endif id="{{$rental->id}}"> 
            <td>{{ $rental->toy->description }}</td> 
            <td>{{ $rental->customer->name }}</td> 
            <td>{{ Carbon\Carbon::parse($rental->init)->format('H:i') }}</td> 
            @if($rental->end != "")
            <td>{{ Carbon\Carbon::parse($rental->end)->format('H:i') }}</td>
            @else 
            <td>{{ Carbon\Carbon::parse($rental->init)->addMinutes($rental->period->time)->format('H:i') }}</td> 
            @endif
            <td>{{ $rental->value_to_pay }}</td> 
            <td class="status">{{$rental->status}}</td>
            <td class="status">
                <div class="col-xs-6">
                    @if($rental->status == "Alugado") 
                        <button type="button" class="btn btn-default btn-pause" aria-label="Left Align">
                            <span class="glyphicon glyphicon-pause" title="Pausar" aria-hidden="true"></span>
                        </button> 
                    @endif
                </div>
                <div class="col-xs-6">
                    @if($rental->status == "Pausado" || $rental->status == "Alugado") 
                        <button type="button" class="btn btn-default btn-finish" aria-label="Left Align">
                            <span class="glyphicon glyphicon-usd" title="Encerrar" aria-hidden="true"></span>
                        </button> 
                    @endif
                </div>
                <div class="col-xs-6">
                    @if($rental->status == "Pausado" || $rental->status == "Alugado" || $rental->status == "Encerrado") 
                        <button type="button" class="btn btn-default btn-cancel" aria-label="Left Align">
                            <span class="glyphicon glyphicon-remove" title="Cancelar" aria-hidden="true"></span>
                        </button> 
                    @endif
                </div>
                <div class="col-xs-6">
                    @if($rental->status == "Pausado" || $rental->status == "Alugado") 
                        <button type="button" class="btn btn-default btn-extra-time" aria-label="Left Align">
                            <span class="glyphicon glyphicon-time" title="Tempo Extra" aria-hidden="true"></span>
                        </button> 
                    @endif
                </div>
            </td> 
        </tr>       
        @endforeach
    </tbody>
</table>

<script>
    $(document).ready(function(){
        //Listeners
        initListeners();
    });

    function initListeners(){
        $(".btn-pause").click(function(){
            var id = $(this).closest('tr').attr('id');
            showLoader();
            $.get("/rental/pause/" + id, function(data){
                loadRentals();
            })
            .fail(function(xhr, status, error) {
                alert(status + ' - ' + error);
            });
        });

        $(".btn-finish").click(function(){
            var id = $(this).closest('tr').attr('id');
            $("#btn-save-finish").val(id);
            showLoader();
            $.get("/rental/calcule/" + id, function(data){
                $("#period-time").text(data.period.time);
                $("#time_total").text(data.timeTotal);
                $("#time_considered").text(data.timeTotal);
                $("#time-exceeded").text(data.timeExceeded);
                $("#value-exceeded").text(data.valueExceeded);
                $("#value-total").text(data.valueTotal);
                $("#modal-payment").modal('show');
                hideLoader();
            })
            .fail(function(xhr, status, error) {
                alert(status + ' - ' + error);
            });
        });

        $(".btn-cancel").click(function(){
            var id = $(this).closest('tr').attr('id');
            showLoader();
            $.get("/rental/cancel/" + id, function(data){
                loadRentals();
            })
            .fail(function(xhr, status, error) {
                alert(status + ' - ' + error);
            });
        });
    }
</script>

    