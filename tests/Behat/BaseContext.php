<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Coduo\PHPMatcher\PHPMatcher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class BaseContext implements Context
{
    private ?Response $response;

    public function __construct(private readonly KernelInterface $kernel, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @When a client sends a :method request to :path
     */
    public function aClientSendsARequestTo(string $path, string $method): void
    {
        $this->response = $this->kernel->handle(Request::create($path, $method));
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived(): void
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @Given the database is clean
     */
    public function theDatabaseIsClean(): void
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            $tool = new SchemaTool($this->entityManager);

            $db = $this->entityManager->getConnection()->getDatabase();
            $connection = $this->entityManager->getConnection();

            $connection->executeQuery('DROP SCHEMA IF EXISTS public CASCADE');
            $connection->executeQuery('CREATE SCHEMA public');

            $connection->close();
            $connection->connect();

            $tool->createSchema($metadata);
        }
    }

    /**
     * @Given there are :tableName database records
     */
    public function thereAreDatabaseRecords(string $tableName, TableNode $table): void
    {
        $db = $this->entityManager->getConnection();

        $rows = $table->getRows();
        $columns = \array_shift($rows);

        foreach ($rows as $row) {
            $data = \array_combine($columns, \array_map(fn ($field) => '@null@' === $field ? null : $field, $row));
            $db->insert($tableName, $data);
        }
    }

    /**
     * @Then the JSON response should match:
     *
     * @throws \JsonException
     */
    public function jsonResponseShouldMatch(PyStringNode $jsonPattern): void
    {
        $matcher = new PHPMatcher();

        $content = $this->response?->getContent();

        if (!$content) {
            throw new \RuntimeException('No response received');
        }

        \json_decode((string) $jsonPattern, true, 512, JSON_THROW_ON_ERROR);

        if (!$matcher->match($content, (string) $jsonPattern)) {
            throw new \RuntimeException($matcher->error().' - '.$content);
        }
    }

    /**
     * @Then the response HTTP status code should be :code
     */
    public function theResponseHTTPStatusCodeShouldBe(int $expectedStatusCode): void
    {
        Assert::assertEquals($expectedStatusCode, $this->response?->getStatusCode());
    }
}
