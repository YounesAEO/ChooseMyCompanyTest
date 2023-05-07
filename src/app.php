<?php

/************************************
Entry point of the project.
To be run from the command line.
************************************/

include_once(__DIR__.'/utils.php');
include_once(__DIR__.'/config.php');


printMessage("Starting...");

// create an array of filenames
$files = array('regionsjob.xml', 'jobteaser.json');

$jobsImporter = new JobsImporter(SQL_HOST, SQL_USER, SQL_PWD, SQL_DB);

// delete old jobs
$jobsImporter->deleteOldJobs();

// loop through the array of files and echo them all
foreach($files as $file){
	/* import jobs from regionsjob.xml and jobteaser.json */
	$jobsImporter->setFile(RESSOURCES_DIR . $file);
	$count = $jobsImporter->importJobs();

	printMessage("> {count} jobs imported from {file}.", ['{count}' => $count, '{file}' => $file]);
}


/* list jobs */
$jobsLister = new JobsLister(SQL_HOST, SQL_USER, SQL_PWD, SQL_DB);
$jobs = $jobsLister->listJobs();

printMessage("> all jobs ({count}):", ['{count}' => count($jobs)]);
foreach ($jobs as $job) {
    printMessage(" {id}: {reference} - {title} - {publication}", [
    	'{id}' => $job['id'],
    	'{reference}' => $job['reference'],
    	'{title}' => $job['title'],
    	'{publication}' => $job['publication']
    ]);
}


printMessage("Terminating...");
