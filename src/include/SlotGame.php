<?php
namespace game;
use game\AbstractGame, game\GameType;

/*
 * Functions generating a Fruit slot game result
 * Using parameters from http://wizardofodds.com/games/slots/appendix/4/
 */

abstract class SlotPayTable {
    /*
    * PAYLINE               PAYS
    * Three seven	        500
    * Three bars	        100
    * Three plums	        50
    * Three bells	        20
    * Three oranges	        15
    * Three cherries	    10
    * cherry-cherry-any	    5
    * cherry-any-any	    2
    */
    const THREE_SEVEN = 500;
    const THREE_BARS = 100;
    const THREE_PLUMS = 50;
    const THREE_BELLS = 20;
    const THREE_ORANGES = 15;
    const THREE_CHERRIES = 10;
    const CHERRY_CHERRY_ANY = 5;
    const CHERRY_ANY_ANY = 2;
}

abstract class RandomType {
    const MERSENNE_TWISTER  = 1;
    const CSPRNG = 2;
}


class SlotGame extends AbstractGame {

    private $rnd_type = RandomType::CSPRNG;

    // Return game type
    public function gameType() {
        return GameType::FRUIT_SLOT;
    }

    // Launch new game
    public function playGame() {
        // Play slots
        $slot_values = $this->launchFruitSlot(); // Slot values ex. ['plum', 'bell', 'cherry']
        $score = $this->getComboScore($slot_values); // Score for given value
        $indexes = $this->getFruitIndexes($slot_values); // Indexes used by ezslots frontend
        // Format des données
        $result = array("values"=>$slot_values, "indexes"=>$indexes, "score"=>$score, "rnd"=>$this->randomType());
        return $result;
    }

    public function randomType() {
        return $this->rnd_type;
    }

    public function setRandomType($rnd) {
        $this->rnd_type = $rnd;
    }

    public function launchFruitSlot() {
        $min_value = 1;
        $max_value = 20;

        // Generate some random stuff
        $reel_1 = $this->getRandomVal($min_value, $max_value);
        $reel_2 = $this->getRandomVal($min_value, $max_value);
        $reel_3 = $this->getRandomVal($min_value, $max_value);

        $values = $this->identifyValue(array($reel_1, $reel_2, $reel_3));
        return $values;
    }


    public function getRandomVal($min, $max) {
        switch ($this->randomType()) {
            case RandomType::MERSENNE_TWISTER:
                return mt_rand($min, $max);
            case RandomType::CSPRNG:
                return random_int($min, $max);
            default:
                // RandomType::CSPRNG
                return random_int($min, $max);
        }
    }


    public function identifyValue($reels) {
        /*
        * Evaluate the score and get symbols
        * Cherry	5	2	3
        * Orange	4	4	4
        * Bell	    3	4	4
        * Seven	    1	1	1
        * Plum	    3	3	1
        * Lemon	    3	5	6
        * Bar       1	1	1

        *          Reel 1		Reel 2		Reel 3
        *          min	max	    min	max	    min	max
        * Cherry   1	5	    1	2	    1	3
        * Orange   6	9	    3	6	    4	7
        * Bell     10	12	    7	10	    8	11
        * Seven    13	13	    11	11	    12	12
        * Plum     14	16	    12	14	    13	13
        * Lemon    17	19	    15	19	    14	19
        * Bar      20	20	    20	20	    20	20
        */
        $result = array();

        $proba = array();
        $proba['Cherry']= [[1,5],[1,2],[1,3]];
        $proba['Orange']= [[6,9],[3,6],[4,7]];
        $proba['Bell']= [[10,12],[7,10],[8,11]];
        $proba['Seven']= [[13,13],[11,11],[12,12]];
        $proba['Plum']= [[14,16],[12,14],[13,13]];
        $proba['Lemon']= [[17,19],[15,19],[14,19]];
        $proba['Bar']= [[20,20],[20,20],[20,20]];

        foreach ($proba as $key => $value) {
            // Reel 1
            if ($reels[0] >= $value[0][0] && $reels[0] <= $value[0][1])
                $result[0] = $key;
            // Reel 2
            if ($reels[1] >= $value[1][0] && $reels[1] <= $value[1][1])
                $result[1] = $key;
            // Reel 3
            if ($reels[2] >= $value[2][0] && $reels[2] <= $value[2][1])
                $result[2] = $key;
        }
        ksort($result);

        return $result;
    }


    public function getComboScore($combo) {
        // Retriewe score value
        $score = 0;
        if ($combo[0] == "Seven" && $combo[1] == "Seven" && $combo[2] == "Seven")
            $score = SlotPayTable::THREE_SEVEN;
        elseif ($combo[0] == "Bar" && $combo[1] == "Bar" && $combo[2] == "Bar")
            $score = SlotPayTable::THREE_BARS;
        elseif ($combo[0] == "Plum" && $combo[1] == "Plum" && $combo[2] == "Plum")
            $score = SlotPayTable::THREE_PLUMS;
        elseif ($combo[0] == "Bell" && $combo[1] == "Bell" && $combo[2] == "Bell")
            $score = SlotPayTable::THREE_BELLS;
        elseif ($combo[0] == "Orange" && $combo[1] == "Orange" && $combo[2] == "Orange")
            $score = SlotPayTable::THREE_ORANGES;
        elseif ($combo[0] == "Cherry" && $combo[1] == "Cherry" && $combo[2] == "Cherry")
            $score = SlotPayTable::THREE_CHERRIES;
        elseif ($combo[0] == "Cherry" && $combo[1] == "Cherry")
            $score = SlotPayTable::CHERRY_CHERRY_ANY;
        elseif ($combo[0] == "Cherry")
            $score = SlotPayTable::CHERRY_ANY_ANY;

        return $score;
    }


    public function getFruitIndexes($data) {
        $idx = array();
        $idx['Cherry']= 0;
        $idx['Orange']= 1;
        $idx['Bell']= 2;
        $idx['Seven']= 3;
        $idx['Plum']= 4;
        $idx['Lemon']= 5;
        $idx['Bar']= 6;

        return array($idx[$data[0]], $idx[$data[1]], $idx[$data[2]]);
    }
}
