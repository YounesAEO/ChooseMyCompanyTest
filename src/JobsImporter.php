<?php

class JobsImporter
{
    private PDO $db;

    private string $file;

    public function __construct(string $host, string $username, string $password, string $databaseName)
    {   
        /* connect to DB */
        try {
            $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $databaseName, $username, $password);
        } catch (Exception $e) {
            die('DB error: ' . $e->getMessage() . "\n");
        }
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    private function _handleXmlFile(): array {
        // create a list of jobs from the xml file
        $xml = simplexml_load_file($this->file);
        $jobs = [];
        foreach ($xml->item as $item) {
            $jobs[] = new Job(
                (string) $item->ref,
                (string) $item->title,
                (string) $item->description,
                (string) $item->url,
                (string) $item->company,
                (string) $item->pubDate
            );
        }

        return $jobs;
    }

    private function _handleJsonFile(): array {
        // create a list of jobs from the json file
        $json = file_get_contents($this->file);
        $json = json_decode($json, true);
        $jobs = [];

        foreach ($json['offers'] as $item) {
            $publication = date_create_from_format('D M d H:i:s e Y', $item['publishedDate']);
            $jobs[] = new Job(
                (string) $item['reference'],
                (string) $item['title'],
                (string) $item['description'],
                (string) $json['offerUrlPrefix'] . $item['urlPath'],
                (string) $item['companyname'],
                (string) date_format($publication,"Y-m-d")
            );
        }
        
        return $jobs;
    }

    private function _createJobsFromFile(): array
    {
        // check if file has an xml extension or json extension
        $fileExtension = pathinfo($this->file, PATHINFO_EXTENSION);
        
        if($fileExtension == 'xml') {
            return $this->_handleXmlFile();
        } elseif($fileExtension == 'json') {
            return $this->_handleJsonFile();
        } else {
            echo 'File extension not supported';
        } 

        return [];
    }

    public function importJobs(): int
    {
        /* create a list of instances of Job class based on the file extension */
        $jobs = $this->_createJobsFromFile();

        /* import each item */
        $count = 0;
        foreach ($jobs as $job) {
            $this->db->exec('INSERT INTO job (reference, title, description, url, company_name, publication) VALUES ('
                . '\'' . addslashes($job->getReference()) . '\', '
                . '\'' . addslashes($job->getTitle()) . '\', '
                . '\'' . addslashes($job->getDescription()) . '\', '
                . '\'' . addslashes($job->getUrl()) . '\', '
                . '\'' . addslashes($job->getCompany()) . '\', '
                . '\'' . addslashes($job->getPublication()) . '\')'
            );
            $count++;
        }
        return $count;
    }

    public function deleteOldJobs(): void
    {
         /* remove existing items */
        $this->db->exec('DELETE FROM job');
    }
}
