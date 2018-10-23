<?php
/*
 * VictorySlot By DevMahno
 * With parts of frontend from ZeroSlot
 */
session_start();

include("simple-php-captcha/simple-php-captcha.php");
$_SESSION['captcha'] = simple_php_captcha();

if (!isset($_SESSION["house_address"])) {
    $_SESSION["house_address"] = null;
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="VictorySlot">
    <meta name="author" content="devmahno">
    <link rel="icon" href="images/favicon.ico">
    <title>VictorySlot - High Speed Slot-less Gambling</title>

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="style/ezslots.css" rel="stylesheet" type="text/css">
    <link href="style/style.css" rel="stylesheet" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- ezslots -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="script/ezslots.js"></script>
    <!-- ezslots -->
    <!-- main script-->
    <script src="script/slot-game.js"></script>
</head>

<body id="page-top">
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">VictorySlot</a>
        </div>
        <div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="#section1">Slot game</a></li>
                    <li><a href="#section2">Rules</a></li>
                    <li><a href="#section3">Vcash</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div id="section1" class="container-fluid light-bg">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-xs-12 col-sm-12 col-lg-10">
            <h2 class="main-title">Victory Slot</h2>
        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-xs-12 col-sm-12 col-lg-8">
            <!--                    A-->
            <div id="mainZone" class="text-center">
                <h1><img src="images/icon.png" height="128" width="128"></h1>
                <h2>VictorySlot</h2>
                <h4>The worlds fastest slot-less gambling.</h4>
                <br/>
                <p>
                    <button id="start_play" class="btn btn-xl">Play!</button>
                </p>
            </div>
            <!--                    B-->
            <div id="captchaModal" style="display: none;">
                <div class="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Are you human?</h4>
                        </div>
                        <div class="modal-body" style="text-align:center">
                            <h6>
                                <!-- Captcha -->
                                <img src="<?php echo $_SESSION['captcha']['image_src']; ?>" alt="CAPTCHA code">
                                <br><br>

                                <div>
                                    <input id="inputCaptcha" style="text-align:center;"
                                           class="form-control" placeholder="Captcha" name="captcha_code" type="text">
                                </div>
                                <!-- Captcha -->
                            </h6>
                        </div>
                        <div class="modal-footer">
                            <button id="btnCloseCaptcha" type="button" class="btn btn-default">Close</button>
                            <button id="btnConfirmCaptcha" type="button" class="btn btn-primary">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--                    C-->
            <div id="betModal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Place Bet</h4>
                    </div>
                    <div class="modal-body" style="text-align:center">
                        <h6>
                            <div id="rpc_balance"></div>
                            <div id="bet_time_elapsed"></div>
                            <div id="bet_amount"></div>
                            <img id="rpc_qr" height="128" width="128">
                            <br>
                            <a id="rpc_address" href="vcash:">...</a>
                        </h6>
                    </div>
                    <div class="modal-footer">
                        <button id="btnCloseBet" type="button" class="btn btn-default">Close</button>
                    </div>
                </div>
            </div>
            <!--                    D-->
            <div id="betResult" style="display: none;">
                <div class="">
                    <div class="text-center">
                        <!-- ezslots -->
                        <div id="ezslots" class="ezslots"></div>
                        <!-- ezslots -->
                        <br>
                        <br>
                        <div id="results"></div>
                        <div class="row">
                            <div class="center-block game-result">
                                <div class="alert alert-success" role="alert" id="bet_status_win" style="display: none;"></div>
                                <div class="alert alert-danger" role="alert" id="bet_status_lose" style="display: none;"></div>
                            </div>
                        </div>
                        <div id="payout_address">
                        </div>
                    </div>
                    <div class="text-center">
                        <br/>
                        <button id="btnCloseBet2" type="button" class="btn btn-default">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
<div id="section2" class="container-fluid newsletter2">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-xs-12 col-sm-12 col-lg-10">
            <h2 class="section-title">Rules</h2>
        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-xs-12 col-sm-6 col-lg-4">
            <!--            A2 -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-info-sign"></span>
                    How to play
                </div>
                <div class="panel-body">
                    <p>
                        VictorySlot is the worlds fastest crypto-currency gambling technology.
                        To play scan the QR code or click the Address and deposit any amount
                        less than or equal to 1.
                    </p>
                    <p>
                        Once the deposit is confirmed the reels will
                        start spinning. Payout is instant and automatic and odds are provably
                        fair.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-lg-4">
            <!--            B2-->
            <div class="panel panel-default">
                <div class="panel-body">
                    <table id="win_table" class="table table-condensed">
                        <tr>

                            <th>Payline</th>
                            <th>Pays</th>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/slot/seven.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/seven.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/seven.png" class="img-thumbnail" width="30" height="30">
                            </td>
                            <td>500</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/slot/bar.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/bar.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/bar.png" class="img-thumbnail" width="30" height="30">
                            </td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/slot/plum.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/plum.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/plum.png" class="img-thumbnail" width="30" height="30">
                            </td>
                            <td>50</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/slot/bell.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/bell.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/bell.png" class="img-thumbnail" width="30" height="30">
                            </td>
                            <td>20</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/slot/orange.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/orange.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/orange.png" class="img-thumbnail" width="30" height="30">
                            </td>
                            <td>15</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/slot/cherry.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/cherry.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/cherry.png" class="img-thumbnail" width="30" height="30">
                            </td>
                            <td>10</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/slot/cherry.png" class="img-thumbnail" width="30" height="30">
                                <img src="images/slot/cherry.png" class="img-thumbnail" width="30" height="30">
                            </td>
                            <td>5</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="images/slot/cherry.png" class="img-thumbnail" width="30" height="30">
                            </td>
                            <td>2</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!--        C2-->
        <div class="col-lg-2"></div>
    </div>
</div>
<div id="section3" class="container-fluid newsletter">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-xs-12 col-sm-12 col-lg-10">
            <h2 class="section-title">Vcash</h2>
        </div>
        <div class="col-lg-1"></div>
    </div>
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-xs-12 col-sm-12 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading"><span class="glyphicon glyphicon-info-sign"></span> What is Vcash?</div>
                <div class="panel-body">Vcash is a decentralized currency for the internet. It enables you to send money to anywhere in the world instantly for almost no cost.</div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"><span class="glyphicon glyphicon-question-sign"></span> Why Vcash?</div>
                <div class="panel-body">Vcash was engineered to be <strong>innovative</strong> and <strong>forward-thinking</strong>. It prevents <strong>eavesdropping</strong> and <strong> censorship</strong>, promotes <strong>decentralized</strong>, <strong>energy efficient</strong> and <strong>instant</strong> network transactions.
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-4">
            <div class="list-group">
                <a class="list-group-item" data-toggle="popover" data-placement="bottom" title="Technology" data-content="Vcash has invented many breakthrough technologies such as ZeroTime to provide sub-second (safely confirmed) transactions and Node Incentives to ensure the network remains robust. Currently we are exploring the area of Darknets."/>
                <h4 class="list-group-item-heading">Technology</h4>
                <p class="list-group-item-text">ZeroTime, Node Incentives and Darknets.</p>
                </a>
                <a class="list-group-item" data-toggle="popover" data-placement="bottom" title="Competency" data-content="Vcash is not a descendant of Bitcoin. While it shares the same basic network and cryptographic principles it's code is written using a backwards compatible but modern approach using C++11. The Vcash developer has over 15 years of experience designing and deploying large scale autonomous peer-to-peer systems.">
                    <h4 class="list-group-item-heading">Competency</h4>
                    <p class="list-group-item-text">Engineered to have breakthrough performance and long-term stability.</p>
                </a>
                <a class="list-group-item">
                    <h4 class="list-group-item-heading" data-toggle="popover" data-placement="bottom" title="Transparency" data-content="Anyone can download the source code and check it out for themselves. This makes the software more secure because it is peer reviewed by a large scale audience.">Transparency</h4>
                    <p class="list-group-item-text">The network protocols and source code are open for peer review.</p>
                </a>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <h4>Powered By <a class="vcash_link" href="https://vcash.community/" >Vcash</a></h4>
</footer>
<!-- Footer -->
</body>
</html>
