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
        padding: 9px 6px 14px 6px;    
    }
    .btn-close:hover {
        background:#ccc;
        color:#fff;
    }
    .btn-close:before{
        content:"x";
    }
    .toy-img{
        max-height: 80px;
    }
    .toy-data{
        height: 270px;
        padding: 10px;
    }
    .toy-row{
        padding:5px 0 0 0;
    }
    .toy-description{
        margin-top: 5px;
    }
    .toy-pays{
        width: 100%;
        padding-top: 5px;
        display: inline-block;
    }
    .btn-pay{
        width: 32%;
    }
    .toy-bottom {
        background-color: #AAA;
        padding: 5px;
    }
    .btn-customer:hover, .btn-status:hover, .btn-period:hover, .btn-time:hover{
        cursor: pointer;
        background: #ccc;
    }
    .value-init, .label-init{
        display: none;
    }
</style>

@foreach($toys as $toy)
    <div class="col-md-2 col-sm-3 col-xs-6 card-container" data-rental="{{ $toy }}">
        @if($toy->rental != null) 
            <span class="btn-close"></span>
        @endif
        <div class="card" @if($toy->rental && $toy->rental->period->time < $toy->rental->time_diff) style="background-color: #ffa1a1" @endif>
            <div class="toy-data"> 
                <img 
                    src="{{ $toy->image ? '/images/toys-img/' . $toy->image : '/images/Imagem_Indisponível.png' }}" 
                    class="img-responsive center-block toy-img"/>
                <div class="text-center">{{ $toy->description }}</div> 
                @if($toy->rental)
                    <div class="btn-customer text-center"><b>{{ $toy->rental->customer->name }}</b></div> 
                    <div class="btn-period col-md-6 toy-row">
                        <div class="col-md-12"><b>Período</b></div>
                        <div class="col-md-12"> {{ $toy->rental->period->time }} min</div>
                    </div> 
                    <div class="btn-time col-md-6 toy-row">
                        <div class="col-md-12 label-end"><b>Retorno</b></div>
                        <div class="col-md-12 label-init"><b>Inicio</b></div>
                        <div class="col-md-12 value-end"> {{ Carbon\Carbon::parse($toy->rental->init)->addMinutes($toy->rental->period->time)->format('H:i') }} </div>
                        <div class="col-md-12 value-init"> {{ Carbon\Carbon::parse($toy->rental->init)->format('H:i') }} </div>
                    </div> 
                    <div class="col-md-6 toy-row">
                        <div class="col-md-12"><b>Tempo</b></div>
                        <div class="col-md-12"> {{ $toy->rental->time_considered }} +
                            <span>{{ $toy->rental->extra_tiem }}</span>
                        </div>
                    </div> 
                    <div class="col-md-6 toy-row">
                        <div class="col-md-12"><b>Valor</b></div>
                        <div class="col-md-12"> 
                            {{ $toy->rental->value_to_pay }}  
                        </div>
                    </div> 
                    <div class="toy-pays text-center"> 
                        <buttom data-value="cd"class="btn btn-primary btn-pay">CD</buttom>
                        <buttom data-value="cc" class="btn btn-primary btn-pay">CC</buttom>
                        <buttom data-value="di"class="btn btn-primary btn-pay">DI</buttom>
                    </div>
                @endif
            </div>    
            <div class="toy-bottom text-center btn-status"> 
                <b>{{ $toy->rental? $toy->rental->status : "Disponível" }}</b> 
            </div>
        </div>
    </div>   
@endforeach

<script>
    $(document).ready(function(){
        //Listeners
        initListeners();
    });

    function initListeners(){
        $(".toy-img").dblclick(function(){
            toy = $(this).parent().parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            if(toy.rental || !$("#name").val()){
                return;
            } else {
                toy.rental = new Object();
                toy.rental.customer =       customer;
                toy.rental.kiosk_id =       kiosk_id;
                toy.rental.toy_id =         toy.id;
                toy.rental.period =         periods[0];
                registerRental();
            }
        });

        $(".btn-customer").dblclick(function(){
            toy = $(this).parent().parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            customer = toy.rental.customer;
            $('#name').val(customer.name);
            $('#id').val(customer.id);
            $('#cpf').val(customer.cpf);
            return;
        });

        function registerRental(){
            showLoader();
            toy.rental._token = "{{ csrf_token() }}";
            $.post("/rental", toy.rental, function(data){
                //reload toys and rentals
                loadRentals();
                $(".clear").val("");
                $(".clear").text("");
                // validateCustomer();
                hideLoader();
            });
        }
        $(".btn-period").dblclick(function(){
            toy = $(this).parent().parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            showLoader();
            $.post("/rental/next-period/" + toy.rental.id, {_token: "{{ csrf_token() }}"}, function(){
                loadRentals();
                hideLoader();
            });

        });
        $(".btn-time").dblclick(function(){
            debugger;
            var init = $(this).closest(".value-init");
            init.css("display", "block");
            $(this).closest(".value-init").css("display", "block");
            $(this).closest(".value-end").css("display", "none");
        });
        $(".btn-status").dblclick(function(){
            toy = $(this).parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            var endPoint = "";
            if(toy.rental && toy.rental.status.indexOf('Alugado') != -1)
            {
                endPoint = "/rental/pause/";
                showLoader();
                $.post(endPoint + toy.rental.id, {_token: "{{ csrf_token() }}"}, function(data){
                    hideLoader();
                    loadRentals();
                });
            }
        });

        $(".btn-pay").dblclick(function(){
            debugger;
            toy = $(this).parent().parent().parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            toy.rental.payment_way = $(this).attr("data-value");
            toy.rental._token = "{{ csrf_token() }}";
            showLoader();
            $.post("/rental/finish", toy.rental, function(){
                loadRentals();
                hideLoader();
            });
        });

        $(".btn-close").click(function(){
            toy = $(this).parent().attr("data-rental");
            toy = JSON.parse(toy);

            showLoader();
            $.post("/rental/cancel/" + toy.rental.id, {_token: "{{ csrf_token() }}"},function(data){
                loadRentals();
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

    