<style>
    div.card-container {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    }
    div.card-container {
        margin-top: 15px;
    }
    div.card {
        height: 320px;
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
        height: 290px;
        padding: 10px;
    }
    .toy-row{
        padding:5px 0 0 0;
    }
    .toy-row div{
        padding: 0px;
        text-align: center;
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
        width: 31%;
    }
    .toy-bottom {
        background-color: #AAA;
        padding: 5px;
    }
    .btn-customer:hover, .btn-status:hover, .btn-period:hover, .btn-time:hover, .btn-extra-time:hover, .btn-value:hover{
        cursor: pointer;
        background: #ccc;
    }
    .toy-init, .label-init{
        display: none;
    }

    .Alugado {
        background: #AAF;
    }
    .Pausado {
        background: #FFA;
    }
    .Disponivel {
        background: #AFA
    }
</style>
@if(count($toys) == 0)
    <h2>Para começar você deve cadastrar pelo menos um <a href="toy">Brinquedo</a> para o quiosque "{{ $kiosk->name }}"</h2><!--(<a href="kiosk">Trocar quiosque</a>)-->
@endif
@foreach($toys as $toy)
    <div class="col-md-2 col-sm-3 col-xs-6 card-container" data-rental="{{ $toy }}">
        @if($toy->rental != null) 
            <span class="btn-close"></span>
        @endif
        <div class="card" @if($toy->rental && $toy->rental->period->time < $toy->rental->time_diff) style="background-color: #ffa1a1" @endif>
            <div class="toy-data"> 
                <img 
                    src="{{ $toy->image ? '/images/toys-img/' . $toy->image : '/images/imagem_indisponivel.png' }}" 
                    class="img-responsive center-block toy-img"/>
                <div class="text-center">{{ $toy->description }}</div> 
                @if($toy->rental)
                    <div class="btn-customer text-center"><b>{{ $toy->rental->customer->name }}</b></div> 
                    <div class="btn-period col-xs-6 toy-row">
                        <div class="col-xs-12"><b>Período</b></div>
                        <div class="col-xs-12"> {{ $toy->rental->period->time }} min</div>
                    </div> 
                    <div class="btn-time col-xs-6 toy-row">
                        <div class="col-xs-12 toy-end"><b>Retorno</b></div>
                        <div class="col-xs-12 toy-init"><b>Inicio</b></div>
                        <div class="col-xs-12 toy-end"> {{ Carbon\Carbon::parse($toy->rental->init)->addMinutes($toy->rental->period->time)->addMinutes($toy->rental->extra_time)->format('H:i') }} </div>
                        <div class="col-xs-12 toy-init"> {{ Carbon\Carbon::parse($toy->rental->init)->format('H:i') }} </div>
                    </div> 
                    <div class="btn-extra-time col-xs-6 toy-row">
                        <div class="col-xs-12"><b>Tempo</b></div>
                        <div class="col-xs-12"> {{ $toy->rental->time_diff }} +
                            <span>{{ $toy->rental->extra_time }}</span>
                        </div>
                    </div> 
                    <div class="btn-value col-xs-6 toy-row">
                        <div class="col-xs-12"><b>Valor</b></div>
                        <div class="col-xs-12"> 
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
            <div class="toy-bottom text-center btn-status {{ $toy->rental ? $toy->rental->status : 'Disponivel' }}"> 
                <b>{{ $toy->rental ? $toy->rental->status : "Disponível" }}</b> 
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
            customer.change_toy = true;
            customer.rental_id = toy.rental.idç
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
                // $(".clear").val("");
                // $(".clear").text("");
                // validateCustomer();
                hideLoader();
            })
            .fail(function(xhr, status, error) {
                alert(status + ' - ' + error);
            });
        }
        $(".btn-period").dblclick(function(){
            toy = $(this).parent().parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            showLoader();
            $.post("/rental/next-period/" + toy.rental.id, {_token: "{{ csrf_token() }}"}, function(){
                loadRentals();
                hideLoader();
            })
            .fail(function(xhr, status, error) {
                alert(status + ' - ' + error);
            });

        });
        $(".btn-time").dblclick(function(){
            var init = $(this).find(".toy-init");
            var end = $(this).find(".toy-end");
            if(init.css("display") == "none"){
                init.css("display", "block");
                end.css("display", "none");
            } else {
                init.css("display", "none");
                end.css("display", "block");
            }
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
                })
                .fail(function(xhr, status, error) {
                    alert(status + ' - ' + error);
                });
                return;
            }

            if(toy.rental && toy.rental.status.indexOf('Pausado') != -1)
            {
                endPoint = "/rental/start/";
                showLoader();
                $.post(endPoint + toy.rental.id, {_token: "{{ csrf_token() }}"}, function(data){
                    hideLoader();
                    loadRentals();
                })
                .fail(function(xhr, status, error) {
                    alert(status + ' - ' + error);
                });
                return;
            }
            if(toy.rental || $("#name").val()){
                alert('Não é possível voltar o último aluguel enquanto houver um cpf de cliente digitado');
                return;
            }
            if (confirm('Você irá voltar o último aluguel para este carrinho dos últimos 5 minutos, confirma?')) {
                endPoint = "/rental/back/";
                showLoader();
                $.get(endPoint + toy.id, {_token: "{{ csrf_token() }}"}, function(data){
                    hideLoader();
                    loadRentals();
                })
                .fail(function(xhr, status, error) {
                    hideLoader();
                    alert(xhr.responseText);
                });
            }

        });

        $(".btn-pay").dblclick(function(){
            toy = $(this).parent().parent().parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            toy.rental.payment_way = $(this).attr("data-value");
            toy.rental._token = "{{ csrf_token() }}";
            showLoader();
            $.post("/rental/finish", toy.rental, function(){
                loadRentals();
                hideLoader();
            })
            .fail(function(xhr, status, error) {
                alert(status + ' - ' + error);
            });
        });

        $(".btn-extra-time").dblclick(function(){
            toy = $(this).parent().parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            $("#btn-save-extra-time").val(id);
            $("#extra-time").html(toy.rental.extra_time);
            $("#modal-extra-time").modal('show');
        });
        
        $(".btn-value").dblclick(function(){
            toy = $(this).parent().parent().parent().attr("data-rental");
            toy = JSON.parse(toy);
            $("#value-total").html(toy.rental.value_to_pay);
            $("#modal-payment").modal('show');
        });

        $(".btn-close").click(function(){
            toy = $(this).parent().attr("data-rental");
            toy = JSON.parse(toy);
            $("#modal-cancel").modal('show');
        });
    
    }
</script>

    
