<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Toy Rental</title>

    <!-- Custom fonts for this template -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link href="{!! asset('vendor/font-awesome/css/font-awesome.min.css') !!}" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{!! asset('vendor/bootstrap/css/bootstrap.min.css') !!}" rel="stylesheet">
    
    <!-- Plugin CSS -->
    <link href="{!! asset('vendor/magnific-popup/magnific-popup.css') !!}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{!! asset('css/creative/creative.min.css') !!}  " rel="stylesheet">

  </head>

  <body id="page-top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="#page-top">Toy Rental</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#about">Sobre</a>
            </li>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#services">Serviços</a>
            </li>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#price">Preço</a>
            </li>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#contact">Contato</a>
            </li>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="/login">Login</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <header class="masthead text-center text-white d-flex">
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-10 mx-auto">
            <h1 class="text-uppercase">
              <strong>Toy Rental</strong>
            </h1>
            <hr>
          </div>
          <div class="col-lg-8 mx-auto">
            <p class="text-faded mb-5">Sistema para controle de aluguéis de brinquedos</p>
            <a class="btn btn-primary btn-xl js-scroll-trigger" href="#about">Conheça mais</a>
          </div>
        </div>
      </div>
    </header>

    <section class="bg-primary" id="about">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="section-heading text-white">Nós temos o que você precisa!</h2>
            <hr class="light my-4">
            <p class="text-faded mb-4">Toy Rental tem tudo o que você precisa para acompanhar, monitorar e controlar seu negócio em qualquer lugar! Projetado para agilizar a operação e fornecer relatórios eficazes para seu negócio! Não importa o quanto você cresça, você acompanha quantos quisques precisar</p>
            <a class="btn btn-light btn-xl js-scroll-trigger" href="#services">Mais</a>
          </div>
        </div>
      </div>
    </section>

    <section id="services">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h2 class="section-heading">Oferecemos</h2>
            <hr class="my-4">
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <i class="fa fa-4x fa-diamond text-primary mb-3 sr-icons"></i>
              <h3 class="mb-3">Vizual limpo</h3>
              <p class="text-muted mb-0">Nossa tela de aluguéis é limpa e fácil de usar</p>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <i class="fa fa-4x fa-paper-plane text-primary mb-3 sr-icons"></i>
              <h3 class="mb-3">Acessível</h3>
              <p class="text-muted mb-0">Você pode acessar de qualquer lugar: computador, tablet e celular!</p>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <i class="fa fa-4x fa-newspaper-o text-primary mb-3 sr-icons"></i>
              <h3 class="mb-3">Relatórios</h3>
              <p class="text-muted mb-0">Relatórios rápidos e úteis.</p>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <i class="fa fa-4x fa-heart text-primary mb-3 sr-icons"></i>
              <h3 class="mb-3">Acompanha seu crescimento</h3>
              <p class="text-muted mb-0">O sistema também permite a inclusão de mais de um quiosque e você os acompanha individualmente!</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="price" class="bg-dark text-white">
      <div class="container text-center">
        <h2 class="mb-4">Cadastre-se e comece agora mesmo!</h2>
        <h2 class="mb-4">R$ 150,00<sup>*</sup>/mês por quiosque.</h2> 
        <br/>
        <a class="btn btn-light btn-xl sr-button" href="/register">Cadastre-se</a>
      </div>
    </section>

    <section id="contact">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="section-heading">Entre em contato!</h2>
            <hr class="my-4">
            <p class="mb-5">Tiramos suas dúvidas e damos suporte para suas necessidades!</p>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-4 ml-auto text-center">
            <i class="fa fa-phone fa-3x mb-3 sr-contact"></i>
            <p>11 971425233</p>
          </div>
          <div class="col-lg-4 mr-auto text-center">
            <i class="fa fa-envelope-o fa-3x mb-3 sr-contact"></i>
            <p>
              <a href="mailto:contato@toyrental.com.br">contato@toyrental.com.br</a>
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Bootstrap core JavaScript -->
    <script src="{!! asset('vendor/jquery/jquery.min.js') !!}"></script>
    <script src="{!! asset('vendor/bootstrap/js/bootstrap.bundle.min.js') !!}"></script>

    <!-- Plugin JavaScript -->
    <script src="{!! asset('vendor/jquery-easing/jquery.easing.min.js') !!}"></script>
    <script src="{!! asset('vendor/scrollreveal/scrollreveal.min.js') !!}"></script>
    <script src="{!! asset('vendor/magnific-popup/jquery.magnific-popup.min.js') !!}"></script>

    <!-- Custom scripts for this template -->
    <script src="{!! asset('js/creative/creative.min.js') !!}"></script>

  </body>

</html>
