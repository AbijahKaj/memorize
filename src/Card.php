<?php
/**
 * Flash card
 *
 * This class handles a single flash card, its properties and its repetition
 * functionality.
 *
 * @copyright 2014 Shahin Zarrabi (shahin@wiwo.se)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Memorize;
 
class Card implements \JsonSerializable
{

    private $question;
    private $answer;
    private $numberOfRepeats;
    private $factor;
    private $nextTime;
    
    /**
     * Constructor
     * 
     * Creates a new flash card with either default or custom settings.
     * 
     * @param String  $question            Flash card question
     * @param String  $answer              Flash card answer
     * @param int     $numberOfRepeats     How many times the card has been repeated
     * @param int     $factor              The card's current E-factor
     * @param int     $nextTime            The next time the card should be repeated as UNIX timestamp
     */
    public function __construct($question = null, $answer = null, $numberOfRepeats = 0, 
                                    $factor = 2, $nextTime = null)
    {
        if (is_null($nextTime)) {
            $nextTime = time();
        }
        
        $this->question = $question;
        $this->answer = $answer;
        $this->numberOfRepeats = $numberOfRepeats;
        $this->factor = $factor;
        $this->nextTime = $nextTime;
    }

    /**
     * Set flash card question
     * @param string $question
     *
     * @return Card
     */
    public function setQuestion(string $question): Card
    {
        $this->question = $question;
        return $this;
    }
    
    /**
     * Get flash card question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set flash card answer
     * @param string $answer
     *
     * @return Card
     */
    public function setAnswer(string $answer): Card
    {
        $this->answer = $answer;
        return $this;
    }
    
    /**
     * Get flash card answer
     * @return string
     */
    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    /**
     * Set number of repetitions
     * @param int $numberOfRepeats
     *
     * @return Card
     */
    public function setNumberOfRepeats(int $numberOfRepeats): Card
    {
        $this->numberOfRepeats = $numberOfRepeats;
        return $this;
    }
    
    /**
     * Get number of repetitions
     * @return int
     */
    public function getNumberOfRepeats(): int
    {
        return $this->numberOfRepeats;
    }

    /**
     * Set E-factor
     * @param float $factor
     *
     *
     * @return Card
     */
    public function setFactor(float $factor): Card
    {
        $this->factor = $factor;
        return $this;
    }
    
    /**
     * Get E-factor
     * @return float
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set next repetition occurrence (UNIX timestamp)
     * @param int $nextTime
     *
     * @return Card
     */
    public function setNextTime(int $nextTime): Card
    {
        $this->nextTime = $nextTime;
        return $this;
    }
    
    /**
     * Get next repetition occurrence (UNIX timestamp)
     * @return int
     */
    public function getNextTime(): ?int
    {
        return $this->nextTime;
    }

    /**
     * Repeat the card
     *
     * This method takes an instance of SM2 and a quality factor to update
     * the flash card accordingly after a repetition.
     *
     * @param SM2 $SM2 An instance of an SM2 object
     * @param int $quality The quality of the answer
     */
    public function repeat(SM2 $SM2, int $quality)
    {
        if ($quality >= 2) {
            $this->numberOfRepeats++;
        } else {
            $this->numberOfRepeats = 1;
        }
                
        $newFactor = $SM2->calcNewFactor($this->factor, $quality);
        $this->factor = $newFactor;
        
        $interval = $SM2->calcInterval($this->numberOfRepeats, $newFactor);
        $this->nextTime = time() + $interval*24*60*60;
    }
    
    /**
     * Encode the card in JSON
     *
     * @return array The JSON encoded Card object
     */
    public function jsonSerialize(): array
    {
        return [
            'question'        => $this->question,
            'answer'          => $this->answer,
            'numberOfRepeats' => $this->numberOfRepeats,
            'factor'          => $this->factor,
            'nextTime'        => $this->nextTime
        ];
    }
    
    /**
     * toJson method for semantic purposes.
     *
     * @return String JSON encoded Card object
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Create Card from JSON encoded string.
     *
     * @param String $json
     * @return Card
     */
    public static function fromJson(string $json): Card
    {
        $card = json_decode($json, true);
        return new Card($card['question'],$card['answer'],$card['numberOfRepeats'],
                            $card['factor'],$card['nextTime']);
    }
    
    /**
     * Convert the Card object to an array.
     *
     * @return array Card object as array.
     */
    public function toArray(): array
    {
        return array(
            'question'        => $this->question,
            'answer'          => $this->answer,
            'numberOfRepeats' => $this->numberOfRepeats,
            'factor'          => $this->factor,
            'nextTime'        => $this->nextTime
        ); 
    }

    /**
     * Create card from array.
     *
     * @param array $card
     * @return Card
     */
    public static function fromArray(array $card): Card
    {
        return new Card($card['question'],$card['answer'],$card['numberOfRepeats'],
                            $card['factor'],$card['nextTime']);
    }

}