<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 30/08/2017
 * Time: 14:25
 */

namespace JPuminate\Architecture\EventBus\Events\Resolvers;


use JPuminate\Architecture\EventBus\Exceptions\EventResolverException;

class GithubEventResolver implements EventResolver
{

    /**
     * @var
     */
    private $reference;
    /**
     * @var
     */
    private $directory_path;


    private $client;
    /**
     * @var
     */
    private $username;
    /**
     * @var
     */
    private $repo;

    public function __construct($username, $repo, $reference, $directory_path)
    {
        $this->client = new \Github\Client();
        $this->reference = $reference;
        $this->directory_path = $directory_path;
        $this->username = $username;
        $this->repo = $repo;
    }

    public function resolve($event)
    {
        try {
            return $this->client->api('repo')
                ->contents()
                ->download($this->username, $this->repo, $this->directory_path.'/'.$this->getEventFile($event), $this->reference);
        } catch (\Exception $e) {
            throw new EventResolverException($event, $this->getAdapter());
        }
    }


    public function getAllEvents()
    {
        try {
            $events = [];
            $contents = $this->client->api('repo')
                ->contents()
                ->show($this->username, $this->repo, $this->directory_path, $this->reference);
            foreach ($contents as $content) {
                array_push($events, $this->getEventName($content));
            }
            return $events;
        }
        catch (\Exception $e){
            throw new EventResolverException(null, null);
        }
    }

    public function getAdapter()
    {
       return "github";
    }

    private function getEventName($content)
    {
        return ucfirst(explode('.php', $content['name'])[0]);
    }

    private function getEventFile($event){
        return ucfirst($event).'.php';
    }
}
