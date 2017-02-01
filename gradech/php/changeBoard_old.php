<?php
class Board
{   
    public function __construct()
    {   
    }
    
    public $category = array(
        'edu'       => array(
            'src'   => array(4779, 3854, 3860, 3862, 3856, 3858),
            'arch'  => array(39469)
        ),
        'math'      => array(
            'src'   => array(4781, 4157, 4191, 4197, 4163, 4169),
            'arch'  => array(39471)
        ),
        'computer'  => array(
            'src'   => array(4783, 4159, 4193, 4199, 4165, 4171),
            'arch'  => array(39473)
        ),
        'han'       => array(
            'src'   => array(4785, 4161, 4195, 4201, 4167, 4173),
            'arch'  => array(39475)
        )
    );
    
    public function archiveArticles( $target )
    {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        for( $i=0; $i<sizeof($this->category[ $target ]['src']); $i++ )
        {
            $query = 'UPDATE `xe_documents` 
                      SET `module_srl` = ' . $this->category[ $target ]['arch'][0] . '
                      WHERE `module_srl` = ' . $this->category[ $target]['src'][ $i ];
            echo $query;

            // send query
            $result = $db->query($query);
        }
    }
}
?>