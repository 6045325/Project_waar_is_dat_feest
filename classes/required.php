<?php

abstract class Human
{
    abstract public function eat();
    abstract public function sleep();
    abstract public function drink();
}

class Job {
    private string $title;
    private string $description;

    public function __construct(string $title, string $description)
    {
        $this->title = $title;
        $this->description = $description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}

class Man extends Human
{
    private array $jobs = [];

    public function __construct()
    {
        // Composition: Man "has" Job objects
        $this->jobs[] = new Job("Developer", "Writes code");
        $this->jobs[] = new Job("Designer", "Designs interfaces");
    }

    public function eat()
    {
        echo "I am eating\n";
    }

    public function sleep()
    {
        echo "I am sleeping\n";
    }

    public function drink()
    {
        echo "I am drinking\n";
    }

    public function showJobs()
    {
        foreach ($this->jobs as $job) {
            echo $job->getTitle() . ": " . $job->getDescription() . "\n";
        }
    }
}

// Test
$man = new Man();
$man->eat();
$man->showJobs();

?>