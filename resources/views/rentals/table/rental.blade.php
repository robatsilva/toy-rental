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
            <td>{{ Carbon\Carbon::parse($rental->init)->addMinutes($rental->period->time)->format('H:i') }}</td> 
            <td>{{ $rental->value_to_pay }}</td> 
            <td class="status">{{$rental->status}}</td>
            <td class="status">
                <p>
                    @if($rental->status == "Alugado") 
                        <button type="button" class="btn btn-default btn-pause" aria-label="Left Align">
                            <span class="glyphicon glyphicon-pause" title="Pausar" aria-hidden="true"></span>
                        </button> @endif
                    @if($rental->status == "Pausado" || $rental->status == "Alugado") 
                        <button type="button" class="btn btn-default btn-finish" aria-label="Left Align">
                            <span class="glyphicon glyphicon-usd" title="Encerrar" aria-hidden="true"></span> @endif
                        </button>
                </p>
                <p>
                    @if($rental->status == "Pausado" || $rental->status == "Alugado" || $rental->status == "Encerrado") 
                        <button type="button" class="btn btn-default btn-cancel" aria-label="Left Align">
                            <span class="glyphicon glyphicon-remove" title="Cancelar" aria-hidden="true"></span>@endif
                        </button>
                    @if($rental->status == "Pausado" || $rental->status == "Alugado") 
                        <button type="button" class="btn btn-default btn-extra-time" aria-label="Left Align">
                            <span class="glyphicon glyphicon-time" title="Tempo Extra" aria-hidden="true"></span>@endif
                        </button>
                </p>
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
                hideLoader();
                loadRentals();
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
            });
        });

        $(".btn-cancel").click(function(){
            var id = $(this).closest('tr').attr('id');
            showLoader();
            $.get("/rental/cancel/" + id, function(data){
                loadRentals();
                loadToys();
                hideLoader();
            });
        });
        $(".btn-extra-time").click(function(){
            var id = $(this).closest('tr').attr('id');
            $("#btn-save-extra-time").val(id);
            $("#modal-extra-time").modal('show');
        });
        
        $("#btn-save-extra-time").click(function(){
            var rentalId = $(this).val();
            showLoader();
            $.post("/rental/extra-time/", {
                _token: "{{ csrf_token() }}",
                id: rentalId,
                extra_time: $("#extra-time").val(),
                reason_extra_time: $("#reason-extra-time").val()
            }, function(data){
                hideLoader();
                loadRentals();
                $("#modal-extra-time").modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });
        });
    }
</script>

    