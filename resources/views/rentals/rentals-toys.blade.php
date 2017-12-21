<style>
    div.card-container {
        margin-top: 15px;
    }
    div.card {
        height: 300px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.19);
    }
    .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        margin-top:-5px;
        margin-right: 5px;
        cursor:pointer;
        color: #fff;
        border: 1px solid #AEAEAE;
        border-radius: 30px;
        background: #605F61;
        font-size: 20px;
        font-weight: bold;
        line-height: 0px;
        padding: 10px 5px 13px 6px;    
    }
    .btn-close:hover {
        background:#ccc;
        color:#fff;
    }
    .btn-close:before{
        content:"x";
    }
    .img-toy{
        max-height: 80px;
    }
    .toy-data{
        height: 270px;
        padding: 10px;
    }
    .toy-row{
        padding: 5px;
    }
    .toy-description{
        margin-top: 5px;
    }
    .toy-pay{
        padding: 0;
        width: 50px;
        height: 50px;
    }
    .toy-bottom {
        background-color: #AAA;
        padding: 5px;
    }
</style>
@foreach($rentals as $rental)
    <div class="col-md-2 col-sm-3 col-xs-6 card-container">
        @if($rental->id != null) 
            <span class="btn-close"></span>
        @endif
        <div class="card" @if($rental->time_exceded > 0) style="background-color: #ffa1a1" @endif>
            <div class="text-center toy-data"> 
                <img src="{{ $rental->image ? '/images/toys-img/' . $rental->image : '/images/Imagem_Indisponível.png' }}" class="img-responsive center-block img-toy">
                <div class="toy-description">{{ $rental->description }}</div> 
                @if($rental->id)
                    <div>
                        <div class="col-md-6 text-left toy-row"><b>Nome:</b></div>
                        <div class="col-md-6 text-right toy-row"> {{ $rental->customer->name }} </div>
                    </div> 
                    <div>
                        <div class="col-md-6 text-left toy-row"><b>Retorno:</b></div>
                        <div class="col-md-6 text-right toy-row"> {{ Carbon\Carbon::parse($rental->init)->addMinutes($rental->period->time)->format('H:i') }} </div>
                    </div> 
                    <div>
                        <div class="col-md-6 text-left toy-row"><b>Valor:</b></div>
                        <div class="col-md-6 text-right toy-row"> 
                            {{ $rental->value_to_pay }}  
                        </div>
                        <div class="toy-row"> 
                            <buttom class="btn btn-primary toy-pay">CD</buttom>
                            <buttom class="btn btn-primary toy-pay">CC</buttom>
                            <buttom class="btn btn-primary toy-pay">DI</buttom>
                        </div>
                    </div> 
                @endif
            </div>    
            <div class="toy-bottom text-center"> <b>{{ $rental->id? $rental->status : "Disponível" }}</b> </div>
        </div>
    </div>   
@endforeach

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

    