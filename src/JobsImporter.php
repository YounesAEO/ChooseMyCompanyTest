<?php

class JobsImporter
{
    private PDO $db;

    private string $file;

    public function __construct(string $host, string $username, string $password, string $databaseName, string $file)
    {
        $this->file = $file;
        
        /* connect to DB */
        try {
            $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $databaseName, $username, $password);
        } catch (Exception $e) {
            die('DB error: ' . $e->getMessage() . "\n");
        }
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

    public function importJobs(): int
    {
        /* remove existing items */
        $this->db->exec('DELETE FROM job');

        /* parse XML file */
        $jobs = _handleXmlFile();

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
}
