<?php namespace Pehape\Traits;

trait HasSeedLoader
{

    /**
     * Seed provider endpoint
     * @var string
     */
    protected $endpoint;

    /**
     * Start seeding
     *
     * @return self
     */
    public function Seed()
    {
        $this->Load($this->endpoint)->doseed();
        // Terminate the service
        $this->Close();
        return $this;
    }

    /**
     * An abstract method to extract the seed data.
     * This method must be re-define in the child class.
     *
     * @return self
     */
    abstract protected function doseed();

}
