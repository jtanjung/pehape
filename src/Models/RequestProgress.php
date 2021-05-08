<?php
namespace Pehape\Models;

use Pehape\Bases\BaseObject;

/**
 * Class RequestInfo
 * @package Pehape\Models
 */
class RequestProgress extends BaseObject {

    /**
     * Current bytes received
     * @var int
     */
  	public $BytesReceived;

    /**
     * Total bytes received
     * @var int
     */
  	public $BytesReceivedTotal;

    /**
     * Current bytes sent
     * @var int
     */
  	public $BytesSent;

    /**
     * Total bytes sent
     * @var int
     */
  	public $BytesSentTotal;

    /**
     * Percentage bytes received
     * @var float
     */
  	public $RatioReceived;

    /**
     * Percentage bytes sent
     * @var float
     */
  	public $RatioSent;

}
