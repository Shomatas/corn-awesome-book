<?php

namespace App\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:get-books'
)]
class GetBooksCommand extends Command
{
    private Client $client;
    private string $token = '';
    const int INVALID_CODE = 401;
    const string DIR_RESPONSE = '/app/response';
    const string PATH_TO_LOCAL_STORAGE_TOKEN = self::DIR_RESPONSE . '/token';
    const string PATH_TO_LOCAL_STORAGE_RESPONSE = self::DIR_RESPONSE . '/response.json';
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->client = new Client([
            'base_uri' => 'http://awesome-book:8000',
        ]);
        $this->setTokenFromLocalStorage();
        if (!$this->isValidToken()) {
            $this->generateToken();
            $this->saveToken();
        }
        $booksData = $this->getBooksData();
        $this->saveBooksData($booksData);
        return 0;
    }

    private function setTokenFromLocalStorage(): void
    {
        if (!is_dir(self::DIR_RESPONSE)) {
            mkdir(self::DIR_RESPONSE);
        }
        if (is_file(self::PATH_TO_LOCAL_STORAGE_TOKEN)) {
            $this->token = file_get_contents(self::PATH_TO_LOCAL_STORAGE_TOKEN);
        }
    }

    private function generateToken(): void
    {
        $response = $this->client->request(
            'POST',
            '/login',
            [
                'json' => [
                    'username' => 'admin',
                    'password' => '1234',
                ]
            ]
        );
        $responseArray = json_decode($response->getBody()->getContents(), true);
        $this->token = 'Bearer ' . $responseArray['token'];
    }

    private function saveToken(): void
    {
        file_put_contents(self::PATH_TO_LOCAL_STORAGE_TOKEN, $this->token);
    }

    private function isValidToken(): bool
    {
        $response = $this->client->request(
            'GET',
            '/books',
            [
                'headers' => [
                    'Authorization' => $this->token,
                ],
                'http_errors' => false,
            ]
        );
        return $response->getStatusCode() !== self::INVALID_CODE;
    }

    private function getBooksData(): array
    {
        $response = $this->client->request(
            'GET',
            '/books',
            [
                'headers' => [
                    'Authorization' => $this->token,
                ]
            ]
        );
        return json_decode($response->getBody()->getContents(), true);
    }

    private function saveBooksData(array $booksData): void
    {
        file_put_contents(
            self::PATH_TO_LOCAL_STORAGE_RESPONSE,
            json_encode($booksData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }
}