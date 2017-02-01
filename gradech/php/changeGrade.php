<?php
class Grade
{
    public function __construct()
    {
    }
    
    public function changeGrade( $member )
    {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        for( $i=0; $i<sizeof($member); $i++ )
        {
            $query = 'UPDATE `xe_member_group_member` 
                      SET `group_srl` = 3876
                      WHERE `group_srl` = 3875 and `member_srl` = ' . $member[$i] . '';

            // send query
            $db->query($query);
        }
    }

    // $group에 해당하는 그룹의 멤버를 return
    public function getMember( $group )
    {      
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $query = 'SELECT *
                  FROM `xe_member_group_member`
                  WHERE `group_srl` = ' . $group . '';

        // send query
        $result = $db->query($query);

        if( $result->num_rows > 0 )
        {
            $content = array();
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                array_push($content, $row[member_srl]);
            }
        } else {
            return 0;
        }

        return $content;
    }
    
    public function deleteGrade( $member )
    {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        for( $i=0; $i<sizeof($member); $i++ )
        {
            $query = 'DELETE 
                      FROM `xe_member_group_member` 
                      WHERE `group_srl` = 3875 and `member_srl` = ' . $member[$i] . '';

            // send query
            $db->query($query);
        }
    }
}
?>