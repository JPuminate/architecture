<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 30/08/2017
 * Time: 14:25
 */

namespace JPuminate\Architecture\EventBus\Events\Resolvers;


use Github\Client;
use Http\Client\Exception\NetworkException;
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
    /**
     * @var
     */
    private $pattern;

    public function __construct($username, $repo, $reference, $directory_path, $pattern="*")
    {
        $this->client = new Client();
        $this->reference = $reference;
        $this->directory_path = $directory_path;
        $this->username = $username;
        $this->repo = $repo;
        $this->pattern = $pattern;
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
            $contents = $this->client->api('repos')
                ->contents()
                ->show($this->username, $this->repo, $this->directory_path, $this->reference);
            foreach ($contents as $content) {
                $events = array_merge($events, array_unique($this->getSubFiles([], $content)));
            }
            return $events;
        }
        catch(NetworkException $e){
            throw $e;
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

    private function getSubFiles($array, $content){
        if($content['type'] == 'file') {
            if(strtolower(pathinfo($content['name'], PATHINFO_EXTENSION)) == "php" &&
            preg_match($this->pattern, explode('.php', $content['name'])[0])) return [$content['name']];
            return [];
        }
        else if($content['type'] == 'dir'){
            $dir_name = $content['name'];
            $contents = $this->client->api('repos')
                ->contents()
                ->show($this->username, $this->repo, $content['path'], $this->reference);
            $events = [];
            foreach ($contents as $content) {
                $events = array_merge($events,$this->getSubFiles($events, $content));
            }
            return array_merge($array, [$dir_name => $events]);
        }
    }



}
