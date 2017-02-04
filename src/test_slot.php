<?php
session_start();
//session_destroy();
//session_start();

// Just for test purposes
$_SESSION['captcha']['code'] = "test";
$_SESSION["checked"] = true;

if (!isset($_SESSION["nbr_play"])) {
    $_SESSION["nbr_play"] = 0;
    $_SESSION["bet"] = 0;
    $_SESSION["win"] = 0;
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VictorySlot">
    <meta name="author" content="devmahno">
    <link rel="icon" href="images/favicon.ico">
    <title>VictorySlot - TEST</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!--    <link href="style/bootstrap.css" rel="stylesheet">-->
    <link href="style/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="style/ezslots.css" rel="stylesheet" type="text/css">
    <link href="style/style.css" rel="stylesheet" type="text/css">

    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="script/ezslots.js"></script>

    <script>
        $(function(){
            //  Evaluate the score and get symbols
            //  Cherry	5	2	3
            //  Orange	4	4	4
            //  Bell	3	4	4
            //  Seven	1	1	1
            //  Plum	3	3	1
            //  Lemon	3	5	6
            //  Bar	    1	1	1

            //setting up some sample set sof things we can make a slot machine of
            var images = [
                '<img src="images/slot/cherry.png">',
                '<img src="images/slot/orange.png">',
                '<img src="images/slot/bell.png">',
                '<img src="images/slot/seven.png">',
                '<img src="images/slot/plum.png">',
                '<img src="images/slot/lemon.png">',
                '<img src="images/slot/bar.png">'
            ];

            var ezslot1 = new EZSlots("ezslots1",{"reelCount":3,
                "symbols":images,"height":126,"width":126});

            $("#winwinwin1").click(function(){
                var win_array = [0,0,0];
                $("#ezslots1").empty();
                $("#results").empty();
                console.log("play");
                var rnd_option = $('#rndselect option:selected').val();

                $.getJSON( "ajax.php?cm=test&rnd="+rnd_option, function(data)
                {
                    console.log(data);
                    win_array = data.indexes;
                    $("#results").text("Score: " + data.score + " - " + data.values.join());

                    //using images instead, and more reels
                    ezslot1 = new EZSlots("ezslots1",{"reelCount":3,"winningSet":win_array,
                        "symbols":images,"height":126,"width":126});

                    console.log(ezslot1.win());
                });
            });

            function do_some_tick() {
                intervalPoll = setInterval(function() {
                    $.getJSON( "ajax.php?cm=teststate", function(data)
                    {
                        $("#state").text(data);
                    });
                }, 1000);
            }

            do_some_tick();
        });
    </script>
</head>
<body data-spy="scroll" data-target=".navbar" data-offset="50">
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
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
            <!--                    Zone 2-->
            <div id="ezslots1" style="min-height:180px; text-align:center;"></div>
            <div>
                <br/>
            </div>
            <div style="text-align:center">
                <button id="winwinwin1" class="btn btn-xl">Play!</button>
            </div>
            <div style="text-align:center">
                <br/>
                <div id="results"></div>
                <div id="state"></div>
                <div>
                    <br/>
                    <select id="rndselect">
                        <option value=1>MT_RAND</option>
                        <option value=2>RANDOM_INT</option>
                    </select>
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
<div id="section3" class="container-fluid">
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
    <h4>Php demo slot-engine by DevMahno</h4>
</footer>
<!-- Footer -->
<script>
    $(document).ready(function(){
        // Add scrollspy to <body>
        $('body').scrollspy({target: ".navbar", offset: 50});

        // Add smooth scrolling on all links inside the navbar
        $("#myNavbar a").on('click', function(event) {
            // Make sure this.hash has a value before overriding default behavior
            if (this.hash !== "") {
                // Prevent default anchor click behavior
                event.preventDefault();

                // Store hash
                var hash = this.hash;

                // Using jQuery's animate() method to add smooth page scroll
                // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                $('html, body').animate({
                    scrollTop: $(hash).offset().top
                }, 800, function(){

                    // Add hash (#) to URL when done scrolling (default click behavior)
                    window.location.hash = hash;
                });
            }  // End if
        });
    });
</script>
</body>
</html>