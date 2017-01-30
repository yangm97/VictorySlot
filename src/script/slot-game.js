/**
 * Created by DevMahno
 */
$(document).ready(function() {
    // enum BetStatus
    var BetStatus = {"UNKNOWN":0, "INITED":1, "RECEIVED":2, "LOCKED":3, "CLOSED":4};
    Object.freeze(BetStatus); // BetStatus will be used like an enum

    var intervalPoll = null;
    var stopLoop = false;

    // Slot images
    var images = [
        '<img src="images/slot/cherry.png">',
        '<img src="images/slot/orange.png">',
        '<img src="images/slot/bell.png">',
        '<img src="images/slot/seven.png">',
        '<img src="images/slot/plum.png">',
        '<img src="images/slot/lemon.png">',
        '<img src="images/slot/bar.png">'
    ];

    function show_frame(frame_name) {
        var divs = ["#mainZone", "#captchaModal", "#betModal", "#betResult"];
        for (var idx in divs) {
            if (divs[idx] != frame_name)
                $(divs[idx]).hide();
        }
        $(frame_name).show();
    }

    function stop_check_loop() {
        // Stop check bet result loop!
        clearInterval(intervalPoll);
        stopLoop = true;
    }

    function check_human_and_start_bet() {
        // Just clear input field value
        $('#inputCaptcha').val('');
        // Check first if need to show Captcha
        $.getJSON("submit_captcha.php", function(response) {
            if (response) {
                init_bet_frame();
            }
            else {
                // Unknown user, check if human, solve the captcha
                show_frame("#captchaModal");
            }
        });
    }

    function show_house_balance() {
        $.getJSON( "ajax.php?cm=getbalance", function(data) {
            $("#rpc_balance").text("House Balance: " + data.result);
        });
    }

    function startBet() {
        // Reinit stopLoop
        stopLoop = false;

        $('#bet_status_win').hide();
        $('#bet_status_lose').hide();

        // That call will init a new bet on server and return the reserved house_address for deposit
        $.getJSON("ajax.php?cm=getnewaddress", function(data) {
            betAddress = data.result;
            // Show house_address
            $("#rpc_address").text(betAddress);

            var randomBetAmount =
                Math.random() < 0.5 ? ((1 - Math.random()) *
                (1.0 - 0.1) + 0.1) : (Math.random() * (1.0 - 0.1) + 0.1);
            randomBetAmount = Math.round(randomBetAmount * 100) / 100;
            console.log(randomBetAmount);

            document.getElementById('rpc_address').setAttribute('href',
                'vcash:' + betAddress + '?amount=' + randomBetAmount);

            $("#rpc_qr").attr("src",
                'https://chart.googleapis.com/chart?chs=256x256&cht=qr&chl=' + betAddress
            );

            // If house_address is OK, call tick function for deposit check!
            do_some_tick(betAddress);
        });
    }

    function do_some_tick(house_address) {
        var ticksPoll = 0;
        // Recursive deposit check
        check_deposit(house_address);

        intervalPoll = setInterval(function() {
            // Show house balance
            show_house_balance();
            $("#bet_time_elapsed").text("Elapsed: " + ++ticksPoll);
        }, 1000);
    }

    // Recursive check function
    function check_deposit(house_address) {
        console.log("stopLoop: "+stopLoop);
        var win_array = [0,0,0];
        $("#ezslots").empty();
        $("#results").empty();

        // Detect stop loop flag
        if (stopLoop==true) {
            // Exit function
            stopLoop = false;
            return;
        }

        $.getJSON( "check_winner.php", {house_address : house_address}, function(data)
        {
            // If we have a CLOSED bet, process the result
            if (data.status && data.status == BetStatus.CLOSED) {
                // Show result frame
                show_frame("#betResult");

                // Set stop loop flag
                stop_check_loop();

                // Update win aray
                win_array = data.details.indexes;

                $("#results").text("Score: " + data.details.score);

                // Show slots
                ezslot1 = new EZSlots("ezslots",{"reelCount":3,"winningSet":win_array,
                    "symbols":images,"height":126,"width":126});
                // Show resulting slot combination
                var slot_result = ezslot1.win();

                if (data.reward > 0) {
                    $('#bet_status_win').show();
                    $("#bet_status_win").text("You win " + data.reward + " XVC!");
                }
                else {
                    $('#bet_status_lose').show();
                    $("#bet_status_lose").text("You loose");
                }

                console.log(slot_result);
            }
            else {
                // Continue waiting
                // Wait 3 secs, do another check
                setTimeout(function(){
                    // ReCheck deposit
                    check_deposit(house_address);
                }, 3000);
            }
        });
    }

    function init_bet_frame() {
        // Captcha check is also done serverside
        console.log("Showing bet frame!");
        show_frame("#betModal");

        $("#rpc_qr").show();
        $("#rpc_address").show();

        // Ask the server for a deposit address
        startBet();

        // Show house balance
        show_house_balance();
    }

    // Click play btn
    $("#start_play").click(function(){
        check_human_and_start_bet();
    });

    // Check Captcha completion
    $("#btnConfirmCaptcha").click(function(){
        var value = document.getElementsByName('captcha_code')[0].value;
        $.post("submit_captcha.php", {captcha_code: value}, function(data, status){
            console.log(data);
            if (data) {
                // Show bet frame
                init_bet_frame();
            }
            else {
                alert("Invalid captcha, try again.");
                window.location.reload();
            }
        });
    });

    $("#btnCloseBet, #btnCloseBet2").click(function(){
        // Show main frame and cancel active bet checks
        stop_check_loop();
        $("#bet_time_elapsed").text("Elapsed: " + 0);
        show_frame("#mainZone");
    });

    $("#btnCloseCaptcha").click(function(){
        // Show main frame
        show_frame("#mainZone");
    });


    //---------------------------------------------------------
    // Add scrollspy to <body>
    $('body').scrollspy({target: ".navbar", offset: 50});

    // Add smooth scrolling on all links inside the navbar
    $("#myNavbar").find("a").on('click', function(event) {
        // Make sure this.hash has a value before overriding default behavior
        if (this.hash !== "") {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Store hash
            var hash = this.hash;
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 800, function(){
                // Add hash (#) to URL when done scrolling (default click behavior)
                window.location.hash = hash;
            });
        }
    });
});
