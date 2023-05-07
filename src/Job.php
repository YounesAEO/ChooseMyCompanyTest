<?php
class Job {
    private string $reference;
    private string $title;
    private string $description;
    private string $url;
    private string $company;
    private string $publication;

    public function __construct(string $reference, string $title, string $description, string $url, string $company, string $publication){
        $this->reference = $reference;
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->company = $company;
        $this->publication = $publication;
    }

    //add getters and setters
    public function getReference(): string {
        return $this->reference;
    }

    public function setReference(string $reference): void {
        $this->reference = $reference;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function setUrl(string $url): void {
        $this->url = $url;
    }

    public function getCompany(): string {
        return $this->company;
    }

    public function setCompany(string $company): void {
        $this->company = $company;
    }

    public function getPublication(): string {
        return $this->publication;
    }

    public function setPublication(string $publication): void {
        $this->publication = $publication;
    }


}