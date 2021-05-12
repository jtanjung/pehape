<?php namespace Pehape\Traits;

trait HasSeedLoader
{

    /**
     * URL endpoint
     * @var string
     */
    protected $endpoint;

    /**
     * Proceed the request
     *
     * @return self
     */
    public function Proceed()
    {
        $this->Load($this->endpoint)->doextract();
        // Terminate the service
        $this->Close();
        return $this;
    }

    /**
     * An alias for Proceed method
     *
     * @return self
     */
    public function Seed()
    {
        return $this->Proceed();
    }

    /**
     * An abstract method to extract informations from the page dom/response.
     * This method must be re-define in the child class.
     *
     * @return self
     */
    abstract protected function doextract();

}
