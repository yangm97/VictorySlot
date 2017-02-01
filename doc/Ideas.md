## Ideas for future development

##### A multibet system
> New multibet system to replace the singlebet system

* user asks for a bet and get a deposit address (`house_address`)
* user make a deposit of `x` XVC on `house_address`
* check_deposit function receives funds and sets data in DB
* user has now a `funds` var in Session with detected deposit amount

##### While `funds > 0`, the user can do following actions:
* set a bet amount from funds `bet_amount <= funds`
* launch the bet and `funds -= bet_amount`
* spin the slots and if win send `reward` back to user

##### End session

* user can ask for refund of `funds` left in session
* if session expires with funds in it, do a cron job to check funds left and refund the user
* after refund, close bet in DB


Check security concerns if any  
Funds can be refund only to first deposit address
