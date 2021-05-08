<?php namespace Pehape\Configs;

use Pehape\Bases\BaseConfig;

/**
 * Class DatabaseConfig
 * @package Pehape\Configs
 */
class DatabaseConfig extends BaseConfig
{

	/**
	 * Database domain or ip address
	 * @var string
	 */
		public $Host = 'localhost';

		/**
		 * Database user name
		 * @var string
		 */
		public $UserName;

		/**
		 * Database user password
		 * @var string
		 */
		public $Password;

		/**
		 * Database name
		 * @var string
		 */
		public $Name;

		/**
		 * Database char encoding system
		 * @var string
		 */
		public $Charset = 'utf8mb4';

		/**
		 * Database collation type
		 * @var string
		 */
		public $Collation = 'utf8mb4_general_ci';

		/**
		 * Class constructor
		 *
		 * @param array $attributes
		 * @return void
		 */
		public function __construct( $attributes = NULL )
		{
				parent::__construct( $attributes );
		}

		/**
		 * Validate properties value
		 *
		 * @return boolean
		 */
		protected function DoValidation()
		{
				return (
					$this->Host &&
					$this->Name &&
					$this->UserName &&
					$this->Password &&
					$this->Charset &&
					$this->Collation
				);
		}

}
?>
