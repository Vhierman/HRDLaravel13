<!DOCTYPE html>
<html>

<head>
    <title>We are coming soon</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('template_frontend/assets/fonts/fontawesome/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template_frontend/assets/vendors/bootstrap/grid.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('template_frontend/assets/vendors/YTPlayer/css/jquery.mb.YTPlayer.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('template_frontend/assets/vendors/vegas/vegas.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Work+Sans:300,400,500,700">
    <link rel="stylesheet" type="text/css" id="app-stylesheet"
        href="{{ asset('template_frontend/assets/css/main.css') }}">
</head>

<body>
    <div class="page-wrap" id="root">
        <div class="md-content">
            <div class="hero md-skin-dark"
                style="background-image:url('{{ asset('template_frontend/assets/img/bgPrima.png') }}');">
                <div class="header">
                    <div class="header__left"><span>BSD-Tangerang
                            Selatan</span><span>PT Prima Komponen Indonesia</span><span>HRD-GA</span>
                    </div>
                </div>
                <div class="container">
                    <div class="hero__wrapper">
                        <div class="row">
                            <div class="col-lg-10 col-xs-offset-0 col-sm-offset-0 col-md-offset-0 col-lg-offset-1 ">
                                <div class="hero__title_inner"><span class="hero__icon">V</span>
                                    <h1 class="hero__title">We Are Almost Ready for Launch</h1>
                                    <p class="hero__text">A perfect awesome app for all employees.
                                </div>
                            </div>
                        </div>

                        <div class="countdown__module" data-date="2026/06/30 23:59:59">
                            <p><span>%D</span> Days</p>
                            <p><span>%H</span> Hours</p>
                            <p><span>%M</span> Minutes</p>
                            <p><span>%S</span> Seconds</p>
                        </div>

                        <div class="service-wrapper">
                            <div class="service">

                            </div>
                            <div class="service">

                            </div>
                            <div class="service">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript" src="{{ asset('template_frontend/assets/vendors/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('template_frontend/assets/vendors/jquery.countdown/jquery.countdown.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('template_frontend/assets/vendors/flat-surface-shader/fss.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('template_frontend/assets/vendors/particles.js/particles.js') }}"></script>
    <script type="text/javascript" src="{{ asset('template_frontend/assets/vendors/waterpipe/waterpipe.js') }}"></script>
    <script type="text/javascript" src="{{ asset('template_frontend/assets/vendors/quietflow/quietflow.min.js') }}">
    </script>
    <script type="text/javascript"
        src="{{ asset('template_frontend/assets/vendors/YTPlayer/jquery.mb.YTPlayer.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('template_frontend/assets/vendors/vegas/vegas.min.js') }}"></script>
    <!-- App-->
    <script type="text/javascript" src="{{ asset('template_frontend/assets/js/main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('template_frontend/assets/js/main.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Mengambil elemen countdown
            var $countdown = $('.countdown__module');

            // Mengambil target tanggal dari atribut data-date
            var targetDate = $countdown.data('date');

            if (targetDate) {
                $countdown.countdown(targetDate, function(event) {
                    // Mengisi format %D, %H, %M, %S sesuai format HTML bawaan template Anda
                    $(this).html(event.strftime(
                        '<p><span>%D</span> Days</p>' +
                        '<p><span>%H</span> Hours</p>' +
                        '<p><span>%M</span> Minutes</p>' +
                        '<p><span>%S</span> Seconds</p>'
                    ));
                });
            }
        });
    </script>
</body>

</html>
