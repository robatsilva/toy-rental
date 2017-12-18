
@foreach($rentals as $rental)
<div class="row">
    <div class="col-md-3">
        <div class="text-center" @if($rental->time_exceded > 0) style="background-color: #ffa1a1" @endif id="{{$rental->id}}"> 
            <div>{{ $rental->toy->description }}</div> 
            <div>{{ $rental->customer->name }}</div> 
            <div>{{ Carbon\Carbon::parse($rental->init)->format('H:i') }}</div> 
            @if($rental->end != "")
            <div>{{ Carbon\Carbon::parse($rental->end)->format('H:i') }}</div>
            @else 
            <div>{{ Carbon\Carbon::parse($rental->init)->addMinutes($rental->period->time)->format('H:i') }}</div> 
            @endif
            <div>{{ $rental->value_to_pay }}</div> 
            <div class="status">{{$rental->status}}</div>
            <div class="status">
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
            </div> 
        </div>    
    </div>   
</div>   
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
            $.post("/rental/extra-time  ", {
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

    