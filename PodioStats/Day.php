<?php
namespace PodioStats;

    
class Day
{
    /**
     * 
     * @var int
     */
    private $notices;
    
    /**
     * 
     * @var int
     */
    private $mentions;

    /**
     * 
     * @var \DateTime
     */
    private $date;
    
    public function __construct()
    {
        $this->notices = 0;
        $this->mentions = 0;    
    }
    
	/**
     * @return int $notices
     */
    public function getNotices()
    {
        return $this->notices;
    }

	/**
     * @return int $mentions
     */
    public function getMentions()
    {
        return $this->mentions;
    }

	/**
     * @return \DateTime $date
     */
    public function getDate()
    {
        return $this->date;
    }

    public function addNotice()
    {
        $this->notices++;
    }

    public function addMention()
    {
        $this->mentions++;
    }

	/**
     * @param DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }
    
    public function getPercentMentions(){
        return round($this->mentions/$this->notices,3);
    }
    
    /**
     * 
     * @param \ArrayObject $array
     * @param string $ymd
     * @return \PodioStats\Day
     */
    public static function getDayFromArrayObjectForIndex(\ArrayObject $array, $ymd){
        if (! $array->offsetExists($ymd)) {
            $day = new Day();
            $array->offsetSet($ymd, $day);
        } else {
            $day = $array->offsetGet($ymd);
        }
        return $day;
    }
    
    public static function getArrayAsLineChartJson(\ArrayObject $array)
    {
        $data = array(
            'labels' => array(),
            'datasets' => array(
                array(
                    'fillColor' => 'rgba(220,220,220,0.5)',
                    'data'=>array()
                ),
                array(
                    'fillColor' => 'rgba(151,187,205,0.5)',
                    'data'=>array()
                ),
                array(
                    'fillColor' => '',
                    'data'=>array()
                )
            ),
        );        
        $Yfit = self::getYfitFromArray($array);
        $i = 0;
        /* @var $day Day */
        foreach ($array as $day) {
            $data['labels'][] = $day->getDate()->format('Y-m-d');
            $data['datasets'][0]['data'][] = $day->getNotices();
            $data['datasets'][1]['data'][] = $day->getMentions();
            $data['datasets'][2]['data'][] = $Yfit[$i++];
        }
        return json_encode($data);
    }
    
    public static function getYfitFromArray(\ArrayObject $array)
    {
        $X = array();
        $i = 1;
        /* @var $day Day */
        foreach ($array as $day) {
            $X[] = $i++;
            $Y[] = $day->getNotices();
        }
        
        // Now convert to log-scale for X
        $logX = array_map('log', $X);
        
        // Now estimate $a and $b using equations from Math World
        $n = count($X);
        $square = create_function('$x', 'return pow($x,2);');
        $x_squared = array_sum(array_map($square, $logX));
        $y_squared = array_sum(array_map($square, $Y));
        $xy = array_sum(array_map(create_function('$x,$y', 'return $x*$y;'), $logX, $Y));
        
        $bFit = ($n * $xy - array_sum($Y) * array_sum($logX)) /
        ($n * $x_squared - pow(array_sum($logX), 2));
        
        $aFit = (array_sum($Y) - $bFit * array_sum($logX)) / $n;
    
        $Yfit = array();
        foreach($X as $x) {
            $Yfit[] = $aFit + $bFit * log($x);
        }
        
        return $Yfit;
    }
}
