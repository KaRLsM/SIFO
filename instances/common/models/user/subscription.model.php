<?php
namespace Common;

class UserSubscriptionModel extends \SeoFramework\MysqlModel
{
	public function __construct()
	{
		$this->db = $this->connectDb( 'user' );
	}
	
	public function subscribe( $user_id, Array $type, $language )
	{		
		$sql = <<<QUERY
INSERT INTO
	subscription
SET
	user_id = :user_id,
	type = :type,
	language = :language
ON DUPLICATE KEY UPDATE
	type = :type,
	language = :language
QUERY;
		
		$stmt = $this->db->prepare( $sql );
		$result = $stmt->execute(
			array(
				':user_id' => $user_id,
				':type' => implode( ',', $type ),
				':language' => $language
			)
		);
		
		return ( $stmt->rowCount() === 1 );
	}
}
